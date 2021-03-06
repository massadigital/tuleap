<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\GitLFS\Batch;

use HTTPRequest;
use Tuleap\GitLFS\Batch\Request\BatchRequest;
use Tuleap\GitLFS\Batch\Request\IncorrectlyFormattedBatchRequestException;
use Tuleap\GitLFS\Batch\Response\BatchSuccessfulResponseBuilder;
use Tuleap\GitLFS\Batch\Response\UnknownOperationException;
use Tuleap\GitLFS\HTTP\LFSAPIHTTPAccessControl;
use Tuleap\GitLFS\HTTP\UserRetriever;
use Tuleap\GitLFS\SSHAuthenticate\Authorization\TokenVerifier;
use Tuleap\Layout\BaseLayout;
use Tuleap\Request\DispatchableWithRequestNoAuthz;
use Tuleap\Request\NotFoundException;

class LFSBatchController implements DispatchableWithRequestNoAuthz
{
    /**
     * @var \gitlfsPlugin
     */
    private $plugin;
    /**
     * @var \GitRepositoryFactory
     */
    private $repository_factory;
    /**
     * @var LFSAPIHTTPAccessControl
     */
    private $api_access_control;
    /**
     * @var BatchSuccessfulResponseBuilder
     */
    private $batch_successful_response_builder;
    /**
     * @var \GitRepository
     */
    private $repository;
    /**
     * @var BatchRequest
     */
    private $batch_request;

    /**
     * @var \PFUser
     */
    private $user;

    /**
     * @var UserRetriever
     */
    private $user_retriever;

    public function __construct(
        \gitlfsPlugin $plugin,
        \GitRepositoryFactory $repository_factory,
        LFSAPIHTTPAccessControl $api_access_control,
        BatchSuccessfulResponseBuilder $batch_successful_response_builder,
        UserRetriever $user_retriever
    ) {
        $this->plugin                            = $plugin;
        $this->repository_factory                = $repository_factory;
        $this->api_access_control                = $api_access_control;
        $this->batch_successful_response_builder = $batch_successful_response_builder;
        $this->user_retriever                    = $user_retriever;
    }

    public function process(HTTPRequest $request, BaseLayout $layout, array $variables)
    {
        try {
            $response = $this->batch_successful_response_builder->build(
                new \DateTimeImmutable(),
                $request->getServerUrl(),
                $this->repository,
                $this->batch_request->getOperation(),
                ...$this->batch_request->getObjects()
            );
        } catch (UnknownOperationException $ex) {
            http_response_code(501);
            echo json_encode(['message' => $ex->getMessage()]);
            return;
        }

        echo json_encode($response);
    }

    public function userCanAccess(\URLVerification $url_verification, \HTTPRequest $request, array $variables)
    {
        \Tuleap\Project\ServiceInstrumentation::increment('gitlfs');
        if ($this->repository !== null || $this->batch_request !== null || $this->user !== null) {
            throw new \RuntimeException(
                'This controller expects to process only one request and then thrown away. One request seems to already have been processed.'
            );
        }

        $this->repository = $this->repository_factory->getByProjectNameAndPath(
            $variables['project_name'],
            $variables['path']
        );
        $project = $this->repository->getProject();
        if ($this->repository === null || ! $project->isActive() || ! $this->plugin->isAllowed($project->getID()) ||
            ! $this->repository->isCreated()) {
            throw new NotFoundException(dgettext('tuleap-git', 'Repository does not exist'));
        }

        $json_string = file_get_contents('php://input');
        if ($json_string === false) {
            throw new \RuntimeException('Can not read the body of the request');
        }
        try {
            $this->batch_request = BatchRequest::buildFromJSONString($json_string);
        } catch (IncorrectlyFormattedBatchRequestException $exception) {
            throw new \RuntimeException($exception->getMessage(), 400);
        }

        $this->user = $this->user_retriever->retrieveUser($request, $url_verification, $this->repository, $this->batch_request);

        return $this->api_access_control->canAccess(
            $this->repository,
            $this->batch_request,
            $this->user
        );
    }
}

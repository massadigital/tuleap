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

namespace Tuleap\AgileDashboard\FormElement;

use AgileDashboard_Semantic_InitialEffortFactory;
use Tracker_Artifact;
use Tracker_Artifact_ChangesetFactory;
use Tracker_ArtifactFactory;

class BurnupCalculator
{
    /**
     * @var Tracker_Artifact_ChangesetFactory
     */
    private $changeset_factory;
    /**
     * @var BurnupDao
     */
    private $burnup_dao;
    /**
     * @var Tracker_ArtifactFactory
     */
    private $artifact_factory;
    /**
     * @var AgileDashboard_Semantic_InitialEffortFactory
     */
    private $initial_effort_factory;


    public function __construct(
        Tracker_Artifact_ChangesetFactory $changeset_factory,
        Tracker_ArtifactFactory $artifact_factory,
        BurnupDao $burnup_dao,
        AgileDashboard_Semantic_InitialEffortFactory $initial_effort_factory
    ) {
        $this->changeset_factory      = $changeset_factory;
        $this->burnup_dao             = $burnup_dao;
        $this->artifact_factory       = $artifact_factory;
        $this->initial_effort_factory = $initial_effort_factory;
    }

    public function getValue($artifact_id, $timestamp)
    {
        $backlog_items = $this->getPlanningLinkedArtifactAtTimestamp(
            $artifact_id,
            $timestamp
        );

        $total_effort = 0;
        /**
         * @var $item \AgileDashboard_Milestone_Backlog_BacklogItem
         */
        foreach ($backlog_items as $item) {
            $artifact     = $this->artifact_factory->getArtifactById($item['id']);
            $total_effort += $this->getEffort($artifact, $timestamp);
        }

        return $total_effort;
    }

    private function getPlanningLinkedArtifactAtTimestamp(
        $artifact_id,
        $timestamp
    ) {
        return $this->burnup_dao->searchLinkedArtifactsAtGivenTimestamp($artifact_id, $timestamp);
    }

    /**
     * @return float
     */
    private function getEffort(
        Tracker_Artifact $artifact,
        $timestamp
    ) {
        $semantic_initial_effort = $this->initial_effort_factory->getByTracker($artifact->getTracker());
        /**
         * @var $initial_effort_field \Tracker_FormElement_Field
         */
        $initial_effort_field = $semantic_initial_effort->getField();

        $effort = 0;
        if ($initial_effort_field) {
            $changeset = $this->changeset_factory->getChangesetAtTimestamp($artifact, $timestamp);
            if ($artifact->getValue($initial_effort_field, $changeset)) {
                $effort = $artifact->getValue($initial_effort_field, $changeset)->getValue();
            }
        }

        return (float) $effort;
    }
}
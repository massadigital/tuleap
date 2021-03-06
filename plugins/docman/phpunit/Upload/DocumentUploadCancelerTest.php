<?php
/**
 * Copyright (c) Enalean, 2019. All Rights Reserved.
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

declare(strict_types=1);

namespace Tuleap\Docman\Upload;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Tuleap\Docman\Tus\TusFileInformation;
use Tuleap\ForgeConfigSandbox;

class DocumentUploadCancelerTest extends TestCase
{
    use MockeryPHPUnitIntegration, ForgeConfigSandbox;

    public function testDocumentBeingUploadedIsCleanedWhenTheUploadIsCancelled() : void
    {
        \ForgeConfig::set('tmp_dir', vfsStream::setup()->url());
        $path_allocator = new DocumentUploadPathAllocator();
        $dao            = \Mockery::mock(DocumentOngoingUploadDAO::class);

        $canceler = new DocumentUploadCanceler($path_allocator, $dao);

        $file_information = \Mockery::mock(TusFileInformation::class);
        $item_id          = 12;
        $file_information->shouldReceive('getID')->andReturns($item_id);
        $item_path = $path_allocator->getPathForItemBeingUploaded($item_id);
        mkdir(dirname($item_path), 0777, true);
        touch($path_allocator->getPathForItemBeingUploaded($item_id));

        $dao->shouldReceive('deleteByItemID')->once();

        $canceler->terminateUpload($file_information);
        $this->assertFileNotExists($item_path);
    }

    public function testCancellingAnUploadThatHasNotYetStartedDoesNotGiveAWarning() : void
    {
        \ForgeConfig::set('tmp_dir', vfsStream::setup()->url());
        $path_allocator = new DocumentUploadPathAllocator();
        $dao            = \Mockery::mock(DocumentOngoingUploadDAO::class);

        $canceler = new DocumentUploadCanceler($path_allocator, $dao);

        $file_information = \Mockery::mock(TusFileInformation::class);
        $item_id          = 12;
        $file_information->shouldReceive('getID')->andReturns($item_id);
        $item_path = $path_allocator->getPathForItemBeingUploaded($item_id);

        $dao->shouldReceive('deleteByItemID')->once();

        $canceler->terminateUpload($file_information);
        $this->assertFileNotExists($item_path);
    }
}

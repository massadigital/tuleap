/*
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

export const does_folder_have_any_error = state =>
    state.has_folder_permission_error || state.has_folder_loading_error;

export const is_folder_empty = state => state.folder_content.length === 0;

export const current_folder_title = state => {
    const hierarchy = state.current_folder_ascendant_hierarchy;

    if (hierarchy.length === 0) {
        return state.root_title;
    }

    return hierarchy[hierarchy.length - 1] ? hierarchy[hierarchy.length - 1].title : "";
};

export const user_can_dragndrop = state => state.max_files_dragndrop > 0;

let nb_uploads = 0;

export const global_upload_progress = state => {
    const uploads = state.folder_content.filter(item => item.hasOwnProperty("progress"));

    if (!uploads.length) {
        nb_uploads = 0;
        return 0;
    }

    const total_progress = uploads.reduce((sum, item) => {
        sum += item.progress;

        return sum;
    }, 0);

    nb_uploads = uploads.length > nb_uploads ? uploads.length : nb_uploads;

    const finished_upload_total_progress = (nb_uploads - uploads.length) * 100;

    return Math.trunc((total_progress + finished_upload_total_progress) / nb_uploads);
};

export const is_uploading = state => {
    return Boolean(state.folder_content.find(item => item.is_uploading));
};

/*
 * Copyright (c) Enalean, 2018-2019. All Rights Reserved.
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

const state = {
    user_id: null,
    project_id: null,
    is_loading_folder: true,
    has_folder_permission_error: false,
    has_folder_loading_error: false,
    folder_loading_error: null,
    is_user_administrator: false,
    folder_content: [],
    date_time_format: null,
    current_folder: null,
    current_folder_ascendant_hierarchy: [],
    is_loading_ascendant_hierarchy: false,
    root_title: "",
    folded_items_ids: [],
    folded_by_map: {},
    has_modal_error: false,
    modal_error: null,
    user_can_create_wiki: false,
    max_files_dragndrop: 1,
    max_size_upload: 1,
    is_under_construction: false,
    files_uploads_list: []
};

export default state;

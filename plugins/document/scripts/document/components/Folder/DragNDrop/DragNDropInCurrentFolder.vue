<!--
  - Copyright (c) Enalean, 2019. All Rights Reserved.
  -
  - This file is a part of Tuleap.
  -
  - Tuleap is free software; you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation; either version 2 of the License, or
  - (at your option) any later version.
  -
  - Tuleap is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <div>
        <current-folder-drop-zone
            ref="dropzone"
            v-bind:user_can_dragndrop_in_current_folder="user_can_dragndrop_in_current_folder"
            v-bind:is_dropzone_highlighted="is_dropzone_highlighted"
        />
        <component
            v-bind:is="error_modal_name"
            v-on:error-modal-hidden="errorModalHasBeenClosed"
        />
    </div>
</template>

<script>
import { mapState, mapGetters } from "vuex";
import CurrentFolderDropZone from "./CurrentFolderDropZone.vue";

export default {
    components: { CurrentFolderDropZone },
    data() {
        return {
            main: null,
            error_modal_shown: false,
            is_dropzone_highlighted: false,
            MAX_FILES_ERROR: "max_files",
            MAX_SIZE_ERROR: "max_size"
        };
    },
    computed: {
        ...mapGetters(["user_can_dragndrop"]),
        ...mapState(["current_folder", "max_files_dragndrop", "max_size_upload"]),
        user_can_dragndrop_in_current_folder() {
            return (
                this.user_can_dragndrop && this.current_folder && this.current_folder.user_can_write
            );
        },
        error_modal_name() {
            if (!this.error_modal_shown) {
                return null;
            }

            if (this.error_modal_shown === this.MAX_SIZE_ERROR) {
                return () =>
                    import(/* webpackChunkName: "document-max-size-dragndrop-error-modal" */ "./MaxSizeDragndropErrorModal.vue");
            }

            return () =>
                import(/* webpackChunkName: "document-max-files-dragndrop-error-modal" */ "./MaxFilesDragndropErrorModal.vue");
        }
    },
    created() {
        this.main = document.querySelector(".document-main");
        this.main.addEventListener("dragover", this.ondragover);
        this.main.addEventListener("dragleave", this.ondragleave);
        this.main.addEventListener("drop", this.ondrop);
    },
    beforeDestroy() {
        this.main.removeEventListener("dragover", this.ondragover);
        this.main.removeEventListener("dragleave", this.ondragleave);
        this.main.removeEventListener("drop", this.ondrop);
    },
    methods: {
        ondragover(event) {
            event.preventDefault();
            event.stopPropagation();
            if (this.isDragNDropingOnAModal(event)) {
                return;
            }
            this.is_dropzone_highlighted = true;
        },
        ondragleave(event) {
            event.preventDefault();
            event.stopPropagation();
            this.is_dropzone_highlighted = false;
        },
        ondrop(event) {
            event.preventDefault();
            event.stopPropagation();
            if (this.isDragNDropingOnAModal(event)) {
                return;
            }

            this.is_dropzone_highlighted = false;
            if (!this.user_can_dragndrop_in_current_folder) {
                return;
            }

            if (!event.dataTransfer.files) {
                return;
            }

            const files = event.dataTransfer.files;

            if (files.length > this.max_files_dragndrop) {
                this.error_modal_shown = this.MAX_FILES_ERROR;
                return;
            }

            for (const file of files) {
                if (file.size > this.max_size_upload) {
                    this.error_modal_shown = this.MAX_SIZE_ERROR;
                    return;
                }
            }

            for (const file of files) {
                this.$store.dispatch("addNewUploadFile", [file, this.current_folder]);
            }
        },
        errorModalHasBeenClosed() {
            this.error_modal_shown = false;
        },
        isDragNDropingOnAModal(event) {
            return Boolean(event.target.closest(".tlp-modal"));
        }
    }
};
</script>

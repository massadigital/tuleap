/*
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
 *
 */

import { shallowMount } from "@vue/test-utils";
import localVue from "../../support/local-vue.js";
import PostActionsSection from "./PostActionsSection.vue";
import { create, createList } from "../../support/factories.js";
import { createStoreMock } from "../../support/store-wrapper.spec-helper.js";

describe("PostActionsSection", () => {
    let store;
    let wrapper;

    beforeEach(() => {
        const store_options = {
            state: {
                transitionModal: {
                    is_loading_modal: false,
                    actions: createList("post_action", 2)
                }
            }
        };
        store = createStoreMock(store_options);
        wrapper = shallowMount(PostActionsSection, {
            mocks: {
                $store: store
            },
            localVue
        });
    });

    const skeleton_selector = '[data-test-type="skeleton"]';
    const action_selector = '[data-test-type="action"]';
    const empty_message_selector = '[data-test-type="empty-message"]';

    describe("when loading", () => {
        beforeEach(() => (store.state.transitionModal.is_loading_modal = true));

        it("shows only skeleton", () => {
            expect(wrapper.contains(skeleton_selector)).toBeTruthy();
            expect(wrapper.contains(action_selector)).toBeFalsy();
            expect(wrapper.contains(empty_message_selector)).toBeFalsy();
        });
    });

    describe("when loaded", () => {
        beforeEach(() => (store.state.transitionModal.is_loading_modal = false));

        describe("when no action", () => {
            beforeEach(() => (store.state.transitionModal.actions = []));

            it("shows only empty message", () => {
                expect(wrapper.contains(skeleton_selector)).toBeFalsy();
                expect(wrapper.contains(action_selector)).toBeFalsy();
                expect(wrapper.contains(empty_message_selector)).toBeTruthy();
            });
        });
        describe("when some actions", () => {
            beforeEach(() => (store.state.transitionModal.actions = createList("post_action", 2)));

            it("shows only actions", () => {
                expect(wrapper.contains(skeleton_selector)).toBeFalsy();
                expect(wrapper.contains(action_selector)).toBeTruthy();
                expect(wrapper.contains(empty_message_selector)).toBeFalsy();
            });
            it("shows as many action as stored", () => {
                expect(wrapper.findAll(action_selector).length).toBe(2);
            });
        });
    });

    describe("#getActionId", () => {
        it("returns run job action identifier from id and type", () => {
            const action = create("post_action", {
                id: 3,
                type: "run_job"
            });
            expect(wrapper.vm.getActionId(action)).toBe("run_job_3");
        });
        it("returns run set field action identifier from id, type and field type", () => {
            const action = create("post_action", {
                id: 3,
                type: "set_field_value",
                field_type: "date"
            });
            expect(wrapper.vm.getActionId(action)).toBe("set_field_value_date_3");
        });
    });
});

/*
 * Copyright (c) Enalean, 2018 - 2019. All Rights Reserved.
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

import TransitionMatrixContent from "./TransitionMatrixContent.vue";
import { create } from "../support/factories.js";
import localVue from "../support/local-vue.js";
import store_options from "../store/index.js";
import { createStoreMock } from "../support/store-wrapper.spec-helper.js";

describe("TransitionMatrixContent", () => {
    let store;
    let wrapper;

    beforeEach(() => {
        store = createStoreMock(store_options, { is_operation_running: false });

        wrapper = shallowMount(TransitionMatrixContent, {
            mocks: {
                $store: store
            },
            localVue,
            propsData: {
                from: create("field_value"),
                to: create("field_value"),
                transition: null
            }
        });
    });

    afterEach(() => store.reset());

    const create_transition_selector = '[data-test-action="create-transition"]';
    const delete_transition_selector = '[data-test-action="delete-transition"]';
    const forbidden_selector = '[data-test-type="forbidden-transition"]';
    const spinner_selector = '[data-test-type="spinner"]';
    const transition_configuration_selector = '[data-test-action="configure-transition"]';

    describe("when from equals to", () => {
        beforeEach(() => {
            const from = create("field_value");
            wrapper.setProps({ from, to: from });
        });

        it("transition creation is not possible", () => {
            expect(wrapper.contains(create_transition_selector)).toBeFalsy();
        });
        it("no operation is possible", () => {
            expect(wrapper.contains(forbidden_selector)).toBeTruthy();
        });
    });

    describe("when from and to differs", () => {
        beforeEach(() => {
            wrapper.setProps({
                from: create("field_value", { id: 1, label: "first value" }),
                to: create("field_value", { id: 2, label: "second value" })
            });
        });

        describe("without any transition", () => {
            beforeEach(() => {
                wrapper.setProps({ transition: null });
            });

            it("transition creation is possible", () => {
                expect(wrapper.contains(create_transition_selector)).toBeTruthy();
            });

            describe("during another operation running", () => {
                beforeEach(() => {
                    store.state.is_operation_running = true;
                });

                it("transition creation is disabled", () => {
                    expect(wrapper.find(create_transition_selector).classes()).toContain(
                        "tracker-workflow-transition-action-disabled"
                    );
                });

                describe("when user clicks to create transition", () => {
                    beforeEach(() => {
                        wrapper.find(create_transition_selector).trigger("click");
                    });

                    it("does nothing", () => {
                        expect(store.commit).not.toHaveBeenCalledWith("createTransition");
                    });
                });
            });

            describe("when user clicks to create transition", () => {
                let resolveCreateTransition;

                beforeEach(() => {
                    store.dispatch.and.returnValue(
                        new Promise(resolve => {
                            resolveCreateTransition = resolve;
                        })
                    );
                    wrapper.find(create_transition_selector).trigger("click");
                });

                it("shows a spinner", () => {
                    expect(wrapper.contains(spinner_selector)).toBeTruthy();
                });
                it("creates transition", () => {
                    expect(store.dispatch).toHaveBeenCalledWith("createTransition", {
                        from_id: 1,
                        to_id: 2
                    });
                });

                describe("and new transition successfully saved", () => {
                    beforeEach(async () => {
                        resolveCreateTransition();
                        await wrapper.vm.$nextTick();
                    });

                    it("hides spinner", () => {
                        expect(wrapper.contains(spinner_selector)).toBeFalsy();
                    });
                });
            });
        });

        describe("with a transition", () => {
            const transition = create("transition");

            beforeEach(() => {
                wrapper.setProps({ transition });
            });

            it("shows transition", () => {
                expect(wrapper.contains(delete_transition_selector)).toBeTruthy();
                expect(wrapper.contains(transition_configuration_selector)).toBeTruthy();
            });

            describe("during another operation running", () => {
                beforeEach(() => {
                    store.state.is_operation_running = true;
                });

                it("transition deletion is disabled", () => {
                    expect(wrapper.find(delete_transition_selector).classes()).toContain(
                        "tracker-workflow-transition-action-disabled"
                    );
                });

                describe("when user clicks to delete transition", () => {
                    beforeEach(() => {
                        wrapper.find(delete_transition_selector).trigger("click");
                    });

                    it("does nothing", () => {
                        expect(store.dispatch).not.toHaveBeenCalledWith("deleteTransition");
                    });
                });
            });

            describe("when user clicks to delete transition", () => {
                let deleteTransitionResolve;

                beforeEach(() => {
                    store.dispatch.and.returnValue(
                        new Promise(resolve => {
                            deleteTransitionResolve = resolve;
                        })
                    );
                    wrapper.find(delete_transition_selector).trigger("click");
                });

                it("shows a spinner", () => {
                    expect(wrapper.contains(spinner_selector)).toBeTruthy();
                });
                it("deletes the transition", () => {
                    expect(store.dispatch).toHaveBeenCalledWith("deleteTransition", transition);
                });

                describe("and transition successfully deleted", () => {
                    beforeEach(async () => {
                        deleteTransitionResolve();
                        await wrapper.vm.$nextTick();
                    });

                    it("hides spinner", () => {
                        expect(wrapper.contains(spinner_selector)).toBeFalsy();
                    });
                });
            });
        });
    });
});

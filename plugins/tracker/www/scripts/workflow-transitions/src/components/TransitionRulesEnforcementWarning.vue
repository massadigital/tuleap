<template>
    <div class="tracker-workflow-rules-enforcement-warning">
        <div v-if="are_transition_rules_enforced === true"
             key="enforcement_enabled"
             v-translate
             class="tlp-alert-success"
             data-test-message="rules-enforcement-active"
        >
            Transition rules are currently applied.
        </div>
        <div v-if="are_transition_rules_enforced === false"
             key="enforcement_disabled"
             v-translate
             class="tlp-alert-warning"
             data-test-message="rules-enforcement-inactive"
        >
            Transition rules don't apply yet.
        </div>
    </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
    name: "TransitionRulesEnforcementWarning",
    computed: {
        ...mapGetters(["are_transition_rules_enforced"])
    }
};
</script>

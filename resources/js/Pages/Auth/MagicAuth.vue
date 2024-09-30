<template>
    <div id="magic-form">
        <input id="magic-input" required>
        <button id="magic-submit"> Sign in without password</button>
        <p id="validation-message"></p>
    </div>
</template>

<script setup>
import magic_script from "../../magicmk_integration_ES6_min.js";
import {onMounted} from "vue";

const props = defineProps(
    {
        magic_project_key: {
            type: String,
            required: true
        }
    }
)

onMounted(() => {
    magic_script()

    window.magicmk = {
        project_slug: props.magic_project_key,
        language: '',
        redirect_url: '',
        params: {
            // extra: "parameters",
        }
    };

})
</script>


<style scoped>

</style>

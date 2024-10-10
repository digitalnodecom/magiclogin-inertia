<template>
    <form id="magic-form">
        <input id="magic-input" required>
        <button id="magic-submit"></button>
        <div id="RecaptchaField"></div>
        <p id="validation-message"></p>
    </form>
</template>

<script setup>
import magic_script from "../../magicmk_integration_ES6.js";
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
    window.magicmk = {
        project_slug: props.magic_project_key,
        language: '',
        redirect_url: '',
        params: {
            // extra: "parameters",
        }
    };
    magic_script()


})
</script>


<style scoped>

</style>

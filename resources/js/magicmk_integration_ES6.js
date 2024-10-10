const serverUrl = 'https://magic.mk';
let sendViaSMS = false;
let useCaptcha = false;

async function magic_script() {
    try {
        const response = await fetch(`${serverUrl}/api/project_info/${window.magicmk.project_slug}`, {
            method: 'GET',
            headers: {'Content-Type': 'application/json'},
        });

        if (!response.ok) return null;

        const data = await response.json();
        sendViaSMS = Boolean(data.send_via_sms);

        if (data.use_captcha) {
            useCaptcha = true;

            window.onloadCallback = function () {
                grecaptcha.render('RecaptchaField', {'sitekey': '6Ley-VcqAAAAAIxTx0EV3bKSajPug2D2emuea6gQ'});
                magicAuth();
            };
            const recaptchaScript = document.createElement('script');
            recaptchaScript.src = 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit';
            recaptchaScript.async = true;
            recaptchaScript.defer = true;
            document.head.appendChild(recaptchaScript);
        } else {
            magicAuth();
        }
    } catch (error) {
        console.error('Error initializing Magic Auth:', error);
    }
}

const magicAuth = () => {
    const placeholder = sendViaSMS ? "+46700000000" : "your@email.com";
    const submitText = sendViaSMS ? "Log in via SMS" : "Login via mail";
    const autocomplete = sendViaSMS ? "tel" : "email";

    const magicForm = document.getElementById('magic-form');
    if (!magicForm) {
        console.log("Could not find the div with id 'magic-form'");
        return;
    }

    let magicInput = document.getElementById('magic-input');
    let magicSubmit = document.getElementById('magic-submit');

    if (!magicInput || !magicSubmit) {
        magicForm.innerHTML = `
            <input id="magic-input" placeholder="${placeholder}" required>
            <button id="magic-submit">${submitText}</button>
            <p id="validation-message"></p>`;
        magicInput = document.getElementById('magic-input');
        magicSubmit = document.getElementById('magic-submit');
    }

    magicInput.placeholder = magicInput.placeholder || placeholder;
    magicInput.autocomplete = magicInput.autocomplete || autocomplete;
    magicSubmit.innerHTML = magicSubmit.innerHTML || submitText;

    const validate = sendViaSMS
        ? (phoneNumber) => /^\+[1-9]\d{1,14}$/.test(phoneNumber)
        : (email) => /^[A-Za-z0-9_.+-]+@[A-Za-z0-9-]+\.[A-Za-z]{2,4}$/.test(email);

    magicSubmit.addEventListener('click', handleSubmit.bind(null, validate, magicInput, magicSubmit, magicForm));
};

const handleSubmit = async (validate, magicInput, magicSubmit, magicForm, event) => {
    event.preventDefault();
    magicSubmit.disabled = true;

    if (!validate(magicInput.value)) {
        document.getElementById('validation-message').textContent = `Invalid input: ${magicInput.value}`;
        magicSubmit.disabled = false;
        return;
    }

    let data = sendViaSMS ? {phone: magicInput.value} : {email: magicInput.value};

    if (useCaptcha) {
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            alert('Please complete the reCAPTCHA');
            magicSubmit.disabled = false;
            return;
        }
        Object.assign(data, {'g-recaptcha-response': recaptchaResponse});
    }
    if (window.magicmk) Object.assign(data, window.magicmk);

    try {
        const response = await fetch(`${serverUrl}/api/login/`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error('Login request failed');

        const responseData = await response.json();
        if (!responseData || !responseData.request_id) {
            throw new Error('Invalid response data');
        }

        const recaptchaField = document.getElementById('RecaptchaField');
        if (recaptchaField) {
            recaptchaField.remove();
        }

        if (responseData.project_type_link) {
            handleVerificationLinkLogin(responseData, magicInput, magicForm);
        } else {
            handleCodeLogin(responseData, magicInput, magicForm);
        }
    } catch (error) {
        console.error('Error during login:', error);
        magicForm.innerHTML = `
            <div>Something went wrong. <a href=".">Try again.</a></div>`;
    } finally {
        magicSubmit.disabled = false;
    }
};

const handleVerificationLinkLogin = (data, magicInput, magicForm) => {
    magicForm.innerHTML = `<p id="sent-title">A <b>verification link</b> has been sent to: ${magicInput.value}</p>`;

    const reverbSettings = {
        appId: data.appId,
        key: data.key,
        host: data.host,
        port: data.port,
        scheme: data.scheme
    };

    const wsUrl = `${reverbSettings.scheme === 'https' ? 'wss' : 'ws'}://${reverbSettings.host}:${reverbSettings.port}/app/${reverbSettings.key}?protocol=7&client=js&version=8.4.0-rc2&flash=false`;

    const socket = new WebSocket(wsUrl);

    socket.onopen = function (e) {
        console.log("[open] Connection established");

        // Subscribe to the channel
        const subscribeMessage = {
            event: 'pusher:subscribe',
            data: {
                auth: '',
                channel: `authenticated.${data.request_id}`
            }
        };
        socket.send(JSON.stringify(subscribeMessage));
    };

    socket.onmessage = function (event) {
        const response = JSON.parse(event.data);

        if (response.event === 'App\\Events\\UserAuthenticatedEvent') {
            const authData = JSON.parse(response.data)
            if (authData && authData.token) {
                magicForm.querySelector('#sent-title').textContent = "Login Successful!";
                socket.close();
                redirectToUrl(authData);
                dispatchLoginSuccessEvent(authData);
            }
        }
    };

    socket.onclose = function (event) {
        if (event.wasClean) {
            console.log(`[close] Connection closed cleanly, code=${event.code} reason=${event.reason}`);
        } else {
            console.log('[close] Connection died');
        }
    };

    socket.onerror = function (error) {
        console.log(`[error] ${error.message}`);
    };
};

const handleCodeLogin = (data, magicInput, magicForm) => {
    magicForm.innerHTML += `<p id="sent-title">A <b>verification code</b> has been sent to: ${magicInput.value}</p>`;
    magicInput = document.getElementById('magic-input');
    const magicSubmit = document.getElementById('magic-submit');
    magicSubmit.innerText = "Enter Code";
    magicSubmit.disabled = false;
    magicInput.value = "";
    magicInput.placeholder = "";
    magicInput.autocomplete = "";

    magicSubmit.addEventListener('click', async (event) => {
        event.preventDefault();
        const codeData = {
            code: magicInput.value,
            request_id: data.request_id
        };

        try {
            const response = await fetch(`${serverUrl}/api/code_validate/`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(codeData)
            });

            const validationData = await response.json();
            if (validationData.consumed) {
                magicForm.querySelector('#sent-title').textContent = "Login Successful!";
                redirectToUrl(validationData);
                dispatchLoginSuccessEvent(validationData);
            } else {
                magicForm.querySelector('#sent-title').textContent = "Login failed, try again!";
            }
        } catch (error) {
            console.error('Error validating code:', error);
        }
    });
};

const redirectToUrl = (data) => {
    let redirectTo = data.redirect_url || window.magicmk.redirect_url;
    if (redirectTo) {
        const url = new URL(redirectTo);
        url.searchParams.append('type', 'magic');
        url.searchParams.append('token', data.token);
        url.searchParams.append('project', window.magicmk.project_slug);
        url.searchParams.append('request_id', data.request_id);
        window.location.replace(url.toString());
    }
};

const dispatchLoginSuccessEvent = (data) => {
    const event = new CustomEvent('magicauth-success', {
        detail: {token: data.token, project: window.magicmk.project_slug, request_id: data.request_id}
    });
    dispatchEvent(event);
};

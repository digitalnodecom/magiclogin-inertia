async function magic_script(){let e="https://magic.mk",t=!1;await fetch(`${e}/api/project_info/${window.magicmk.project_slug}`,{method:"GET",headers:{"Content-Type":"application/json"}}).then(e=>e.ok?e.json():null).then(e=>{t=Boolean(e.send_via_sms)});let i=t?"+46700000000":"your@email.com",n=t?"Log in via SMS":"Login via mail",a=t?"tel":"email",o=document.getElementById("magic-form"),l=document.getElementById("magic-input"),r=document.getElementById("magic-submit"),c=document.getElementById("magic-form");if(!o){console.log("Could not find the div with id 'magic-form'");return}l&&r||(c.innerHTML=`
            <input id="magic-input" placeholder="`+i+`" required>
            <button id="magic-submit"> `+n+`</button>
            <p id="validation-message"></p>`,l=document.getElementById("magic-input"),r=document.getElementById("magic-submit")),l.placeholder||(l.placeholder=i),l.autocomplete||(l.autocomplete=a),r.innerHTML||(r.innerHTML=n);let s=e=>/^[A-Za-z0-9_.+-]+@[A-Za-z0-9-]+\.[A-Za-z]{2,4}$/.test(e);function d(e){let t=e.redirect_url||window.magicmk.redirect_url;if(t){let i=new URL(t);i.searchParams.append("type","magic"),i.searchParams.append("token",e.token),i.searchParams.append("project",window.magicmk.project_slug),i.searchParams.append("request_id",e.request_id),window.location.replace(i.toString())}}function m(e){let t=new CustomEvent("magicauth-success",{detail:{token:e.token,project:window.magicmk.project_slug,request_id:e.request_id}});dispatchEvent(t)}t&&(s=e=>/^\+[1-9]\d{1,14}$/.test(e)),r.addEventListener("click",i=>{if(i.preventDefault(),r.disabled=!0,!s(l.value)){document.getElementById("validation-message").textContent=`Invalid input: ${l.value}`,r.disabled=!1;return}let n=t?{phone:l.value}:{email:l.value};window.magicmk&&Object.assign(n,window.magicmk),fetch(`${e}/api/login/`,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(n)}).then(e=>e.ok?e.json():null).then(t=>{var i;if(!t||!t.request_id){c.innerHTML='<div>Something went wrong. <a href=".">Try again.</a></div>',r.disabled=!1;return}t.project_type_link?function t(i){c.innerHTML=`<p id="sent-title">A <b>verification link</b> has been sent to: ${l.value}</p>`;let n=setInterval(()=>{fetch(`${e}/api/check`,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({project_slug:window.magicmk.project_slug,request_id:i.request_id})}).then(e=>200===e.status?e.json():null).then(e=>{e&&e.token&&(c.querySelector("#sent-title").textContent="Login Successful!",clearInterval(n),d(e),m(e))})},2e3)}(t):(i=t,c.innerHTML+=`<p id="sent-title">A <b>verification code</b> has been sent to: ${l.value}</p>`,l=document.getElementById("magic-input"),(r=document.getElementById("magic-submit")).innerText="Enter Code",r.disabled=!1,l.value="",l.placeholder="",l.autocomplete="",(r=document.getElementById("magic-submit")).addEventListener("click",t=>{t.preventDefault();let n={code:l.value,request_id:i.request_id};fetch(`${e}/api/code_validate/`,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(n)}).then(e=>e.json()).then(e=>{e.consumed?(c.querySelector("#sent-title").textContent="Login Successful!",d(e),m(e)):c.querySelector("#sent-title").textContent="Login failed, try again!"})}))}).catch(()=>r.disabled=!1)})}export default magic_script;

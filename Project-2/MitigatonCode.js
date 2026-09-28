//AI Disclosure: AI assissted in the creation of this document

/*Fix for the XSS vulnerability: escape user input before inserting it into the DOM, 
and prefer textContent over innerHTML whenever the content is plain text rather than markup.*/

function displayComment(comment) {
    const el = document.getElementById("comments");
    const p = document.createElement("p");
    p.textContent = comment; // treated as plain text, not parsed as HTML
    el.appendChild(p);
}

// If HTML rendering is genuinely required, sanitize first (e.g., DOMPurify)
function displayRichComment(comment) {
    const clean = DOMPurify.sanitize(comment);
    document.getElementById("comments").innerHTML = clean;
}

/*Fix for the eval() vulnerability: remove eval() entirely and replace it with a purpose-built parser 
that only understands the operations you intend to allow.*/

function calculate(userExpression) {
    // Only allow digits, whitespace, and basic arithmetic operators
    if (!/^[\d\s+\-*/().]+$/.test(userExpression)) {
        throw new Error("Invalid expression");
    }
    return Function('"use strict"; return (' + userExpression + ')')();
}
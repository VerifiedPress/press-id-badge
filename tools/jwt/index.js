const fs = require('fs');
const path = require('path');
const jwt = require('jsonwebtoken');

const args = process.argv.slice(2); // Skip node and script path

let options = {};
args.forEach((arg, index) => {
  if (arg.startsWith('--')) {
    const key = arg.slice(2);
    const value = args[index + 1] && !args[index + 1].startsWith('--') ? args[index + 1] : true;
    options[key] = value;
  }
});

console.log('Parsed options:', options);


// Load keys
const privateKey = fs.readFileSync('../../public/private_key.pem', 'utf8');
const publicKey = fs.readFileSync('../../public/public_key.pem', 'utf8');
const identity_json = fs.readFileSync(path.join(__dirname,'/../../public/.well-known/did.json'), 'utf-8');
const identity = JSON.parse(identity_json);

// Create JWT
const payload = {
  did: identity.id,
  role: 'admin'
};


if (!fs.existsSync("jwt") || options.new) {
    const token = jwt.sign(payload, privateKey, {
        algorithm: 'RS256',
        expiresIn: '1h'
    });

    fs.writeFileSync("jwt", token);
} else if (fs.existsSync("jwt") || options.delete) {
    fs.unlinkSync("jwt");
    return;
}

const token = fs.readFileSync('jwt','utf-8');
console.log('JWT:', token);

// Verify JWT
try {
    const decoded = jwt.verify(token, publicKey, { algorithms: ['RS256'] });
    console.log('Decoded Payload:', decoded);
    const expiryDate = new Date(decoded.exp * 1000); // Convert seconds to milliseconds
    const issuedDate = new Date(decoded.iat * 1000);
    console.log('Token issued at:', issuedDate.toLocaleDateString() , ' at ', issuedDate.toLocaleTimeString());
    console.log('Token will expire at:', expiryDate.toLocaleDateString() , ' at ', expiryDate.toLocaleTimeString());
} catch (err) {
  if (err.name === 'TokenExpiredError') {
    const expiryDate = new Date(err.expiredAt); 
    console.error('Token has expired at:', expiryDate.toLocaleDateString() , ' at ', expiryDate.toLocaleTimeString());
  } else {
    console.error('Token verification failed:', err.message);
  }
}
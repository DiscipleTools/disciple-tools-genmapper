module.exports = {
  "env": {
    "browser": true,
    "es6": true
  },
  "parserOptions": {
    "ecmaVersion": 2017, // enables parsing async functions correctly
  },
  "extends": "eslint:recommended",
  "globals": {
    "jQuery": false,
  },
  "rules": {
    "no-console": "off",
    "no-undef": "warn",
    "no-unused-vars": "warn",
    "no-empty": "warn",
    "no-useless-escape": "warn",
    "strict": "warn",
    "no-implicit-globals": "error",
  },
};

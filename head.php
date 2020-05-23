<link rel="stylesheet" type="text/css" href="css/greenwichcarpool.css">
<link rel="stylesheet" type="text/css" href="css/cookieconsent.min.css" />
<script src="js/cookieconsent.min.js"></script>
<script>
  window.addEventListener("load", function(){
    window.cookieconsent.initialise({
      "palette": {
        "popup": {
          "background": "#aa0000",
          "text": "#ffdddd"
        },
        "button": {
          "background": "#ff0000"
        }
      },
      content: {
        header: 'Cookies used on the website!',
        message: 'This website uses cookies to ensure you get the best experience on our website.',
        dismiss: 'Got it!',
        allow: 'Allow cookies',
        deny: 'Decline',
        link: '',
        href: '',
        close: '&#x274c;',
      },
      "theme": "edgeless"
    })});
  </script>
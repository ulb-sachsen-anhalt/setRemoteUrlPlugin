# Plugin for OMP and OJS

This Plugin provide the ability to update the _remote_url_ of galleys, which is not supported by the formal API by now.

For testing the function you have to copy _this_ PlugIn in the 'generic'-Plugin section of your OJS or OMP installation.
Activate the PlugIn at **Site-level** in Browser and provide a token via settings to secure your Application.

Now test:

<pre>curl -k "https://www.&lt;yourojs&gt;.com?publication_id=123&remote_url=<your DOI URI>&token=&lt;token in Plugin settings&gt;"</pre>

Visit result https://&lt;your ojs server&gt;/&lt;journal which incl. publication_id=123&gt;/api/v1/submissions
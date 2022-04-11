# Plugin for OMP and OJS

The _SetRemoteUrlPlugin_ provides the ability to update the _urlRemote_ of galleys(OJS) or publicationFormat(OMP), which is not supported by the formal API as off now.

For testing the function you have to copy _this_ PlugIn in the 'generic'-Plugin section of your OJS or OMP installation.
Activate the PlugIn at **Site-Level** in your browser and provide a token via settings, to secure your application.

Now test:

<pre>curl -k "https://&lt;your ojs server&gt;?publication_id=123&remote_url=&lt;your DOI URI&gt;&token=&lt;token in Plugin settings&gt;"</pre>

Visit result https://&lt;your ojs server&gt;/&lt;journal which incl. publication_id=123&gt;/api/v1/submissions

Search for _urlRemote_ or &lt;your DOI URI&gt;

If you found a match, you're done.

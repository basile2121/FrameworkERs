<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* event/create.html.twig */
class __TwigTemplate_e5277f8cc5586d05ca20e27f1dcc1534 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<form action=\"/event/save\" method=\"post\" enctype=\"multipart/form-data\">
\t<input type=\"text\" name=\"name\"/>
\t<input type=\"file\" name=\"image\"/>
\t<div>
\t\t<label>Latitude </label>
\t\t<input type=\"text\" id=\"lat\"  value=''/>
\t\t<label>Longitude </label>
\t\t<input type=\"text\" id=\"lang\" value=''/>
\t</div>
\t<input type=\"submit\" value=\"Enregistrer\">
</form>

<div id=\"dvMap\" style=\"width: 100%; height: 500px\"></div>

<script type=\"text/javascript\" src=\"http://maps.googleapis.com/maps/api/js?sensor=false\"></script>
<script type=\"text/javascript\">
        window.onload = function () {
            var mapOptions = {
                center: new google.maps.LatLng(45.74692283737827, 4.835872650146484),
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var infoWindow = new google.maps.InfoWindow();
            var latlngbounds = new google.maps.LatLngBounds();
            var map = new google.maps.Map(document.getElementById(\"dvMap\"), mapOptions);
            google.maps.event.addListener(map, 'click', function (e) {
\t\t\t\tdocument.getElementById(\"lat\").value= e.latLng.lat();
\t\t\t\tdocument.getElementById(\"lang\").value= e.latLng.lng();
            });
        }
</script>";
    }

    public function getTemplateName()
    {
        return "event/create.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<form action=\"/event/save\" method=\"post\" enctype=\"multipart/form-data\">
\t<input type=\"text\" name=\"name\"/>
\t<input type=\"file\" name=\"image\"/>
\t<div>
\t\t<label>Latitude </label>
\t\t<input type=\"text\" id=\"lat\"  value=''/>
\t\t<label>Longitude </label>
\t\t<input type=\"text\" id=\"lang\" value=''/>
\t</div>
\t<input type=\"submit\" value=\"Enregistrer\">
</form>

<div id=\"dvMap\" style=\"width: 100%; height: 500px\"></div>

<script type=\"text/javascript\" src=\"http://maps.googleapis.com/maps/api/js?sensor=false\"></script>
<script type=\"text/javascript\">
        window.onload = function () {
            var mapOptions = {
                center: new google.maps.LatLng(45.74692283737827, 4.835872650146484),
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var infoWindow = new google.maps.InfoWindow();
            var latlngbounds = new google.maps.LatLngBounds();
            var map = new google.maps.Map(document.getElementById(\"dvMap\"), mapOptions);
            google.maps.event.addListener(map, 'click', function (e) {
\t\t\t\tdocument.getElementById(\"lat\").value= e.latLng.lat();
\t\t\t\tdocument.getElementById(\"lang\").value= e.latLng.lng();
            });
        }
</script>", "event/create.html.twig", "C:\\laragon\\www\\FrameworkERs\\templates\\event\\create.html.twig");
    }
}
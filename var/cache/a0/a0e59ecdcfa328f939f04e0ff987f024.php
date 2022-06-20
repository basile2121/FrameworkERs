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
\t<input type=\"submit\" value=\"Enregistrer\">
</form>
";
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
\t<input type=\"submit\" value=\"Enregistrer\">
</form>
", "event/create.html.twig", "C:\\laragon\\www\\FrameworkERs\\templates\\event\\create.html.twig");
    }
}

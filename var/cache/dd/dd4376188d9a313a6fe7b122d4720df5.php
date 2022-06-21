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

/* event/show.html.twig */
class __TwigTemplate_726ee3f9148b140c41c7fd15c9ae3206 extends Template
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
        echo "
<iframe  id=\"map\" width=\"100%\" height=\"500\" src=\"https://maps.google.com/maps?q=";
        // line 2
        echo twig_escape_filter($this->env, ($context["latitude"] ?? null), "html", null, true);
        echo ",";
        echo twig_escape_filter($this->env, ($context["longitude"] ?? null), "html", null, true);
        echo "&output=embed\"></iframe>";
    }

    public function getTemplateName()
    {
        return "event/show.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("
<iframe  id=\"map\" width=\"100%\" height=\"500\" src=\"https://maps.google.com/maps?q={{ latitude}},{{ longitude}}&output=embed\"></iframe>", "event/show.html.twig", "C:\\laragon\\www\\FrameworkERs\\templates\\event\\show.html.twig");
    }
}

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

/* admin/admin_home.html.twig */
class __TwigTemplate_e4e07514e354ca023134f0bd83cedb9c extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("base.html.twig", "admin/admin_home.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo "Menu d'administration";
    }

    // line 6
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 7
        echo "    <h1 class=\"display-1 text-center mt-5\">Espace administrateur</h1>
    <main>
        <a href=\"/admin/evenement/create\">Creer un evenement</a>
        <a href=\"/admin/evenements\">Gerer les evenements</a>
        <a href=\"/admin/utilisateurs\">Gerer les utilisateurs</a>
        <a href=\"/admin/ecoles\">Gerer les ecoles</a>
    </main>
";
    }

    public function getTemplateName()
    {
        return "admin/admin_home.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  58 => 7,  54 => 6,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"base.html.twig\" %}

{% block title %}Menu d'administration{% endblock %}


{% block content %}
    <h1 class=\"display-1 text-center mt-5\">Espace administrateur</h1>
    <main>
        <a href=\"/admin/evenement/create\">Creer un evenement</a>
        <a href=\"/admin/evenements\">Gerer les evenements</a>
        <a href=\"/admin/utilisateurs\">Gerer les utilisateurs</a>
        <a href=\"/admin/ecoles\">Gerer les ecoles</a>
    </main>
{% endblock %}
", "admin/admin_home.html.twig", "C:\\Users\\basil\\Documents\\ESGIcours\\Projet Annuel\\FrameworkERs\\templates\\admin\\admin_home.html.twig");
    }
}

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

/* admin/utilisateurs/admin_form_edit_utilisateur.html.twig */
class __TwigTemplate_e04d190c06eb79937862d82903ed534a extends Template
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
        $this->parent = $this->loadTemplate("base.html.twig", "admin/utilisateurs/admin_form_edit_utilisateur.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo "Modifier l'utilisateur";
    }

    // line 6
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 7
        echo "    <h1 class=\"display-1 text-center mt-5\">Modifier l'utilisateur</h1>
    <main>
        <form class=\"form-admin\" method=\"POST\" action=\"/admin/update/utilisateurs\">
            <div class=\"form-group\">
                <label for=\"userName\">Nom</label>
                <input type=\"text\" name=\"nom\" class=\"form-control\" id=\"userName\" value=\"";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["utilisateur"] ?? null), "nom", [], "any", false, false, false, 12), "html", null, true);
        echo "\" placeholder=\"Nom\">
            </div>
            <div class=\"form-group\">
                <label for=\"userFirstName\">Prenom</label>
                <input type=\"text\" name=\"prenom\" class=\"form-control\" id=\"userFirstName\" value=\"";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["utilisateur"] ?? null), "prenom", [], "any", false, false, false, 16), "html", null, true);
        echo "\" placeholder=\"Prénom\">
            </div>
            <div class=\"form-group\">
                <label for=\"userBirthDate\">Date de naissance</label>
                <input type=\"date\" name=\"dateNaissance\" class=\"form-control\" id=\"userBirthDate\" value=\"";
        // line 20
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["utilisateur"] ?? null), "dateNaissance", [], "any", false, false, false, 20), "Y-m-d"), "html", null, true);
        echo "\" placeholder=\"Date de naissance\">
            </div>
            <div class=\"form-group\">
                <label for=\"userMail\">Mail</label>
                <input type=\"email\" name=\"mail\" class=\"form-control\" id=\"userMail\" value=\"";
        // line 24
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["utilisateur"] ?? null), "mail", [], "any", false, false, false, 24), "html", null, true);
        echo "\" placeholder=\"Adresse mail\">
            </div>
            <div class=\"form-group\">
                <label for=\"userPhoneNumber\">Téléphone</label>
                <input type=\"text\" name=\"telephone\" class=\"form-control\" id=\"userPhoneNumber\" value=\"";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["utilisateur"] ?? null), "telephone", [], "any", false, false, false, 28), "html", null, true);
        echo "\" placeholder=\"Numéro de téléphone\">
            </div>
            <div class=\"form-group\">
                <label for=\"promotionSelect\">Promotions</label>
                <select name=\"promotion\" id=\"promotionSelect\">
                    ";
        // line 33
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["promotions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["promotion"]) {
            // line 34
            echo "                        <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["promotion"], "idPromotion", [], "any", false, false, false, 34), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["promotion"], "libellePromotion", [], "any", false, false, false, 34), "html", null, true);
            echo "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['promotion'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 36
        echo "                </select>
            </div>
            <div class=\"form-group\">
                <label for=\"rolesSelect\">Roles</label>
                <select name=\"role\" id=\"rolesSelect\">
                    ";
        // line 41
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["roles"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["role"]) {
            // line 42
            echo "                        <option value=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["role"], "idRole", [], "any", false, false, false, 42), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["role"], "libelleRole", [], "any", false, false, false, 42), "html", null, true);
            echo "</option>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['role'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 44
        echo "                </select>
            </div>
            <button type=\"submit\" name=\"id\" class=\"btn btn-primary\" value=\"";
        // line 46
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["utilisateur"] ?? null), "idUtilisateur", [], "any", false, false, false, 46), "html", null, true);
        echo "\">Sauvegarder</button>
            <a href=\"/admin/utilisateurs\" class=\"btn btn-primary\">Annuler</a>
        </form>
    </main>
";
    }

    public function getTemplateName()
    {
        return "admin/utilisateurs/admin_form_edit_utilisateur.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  142 => 46,  138 => 44,  127 => 42,  123 => 41,  116 => 36,  105 => 34,  101 => 33,  93 => 28,  86 => 24,  79 => 20,  72 => 16,  65 => 12,  58 => 7,  54 => 6,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"base.html.twig\" %}

{% block title %}Modifier l'utilisateur{% endblock %}


{% block content %}
    <h1 class=\"display-1 text-center mt-5\">Modifier l'utilisateur</h1>
    <main>
        <form class=\"form-admin\" method=\"POST\" action=\"/admin/update/utilisateurs\">
            <div class=\"form-group\">
                <label for=\"userName\">Nom</label>
                <input type=\"text\" name=\"nom\" class=\"form-control\" id=\"userName\" value=\"{{ utilisateur.nom }}\" placeholder=\"Nom\">
            </div>
            <div class=\"form-group\">
                <label for=\"userFirstName\">Prenom</label>
                <input type=\"text\" name=\"prenom\" class=\"form-control\" id=\"userFirstName\" value=\"{{ utilisateur.prenom }}\" placeholder=\"Prénom\">
            </div>
            <div class=\"form-group\">
                <label for=\"userBirthDate\">Date de naissance</label>
                <input type=\"date\" name=\"dateNaissance\" class=\"form-control\" id=\"userBirthDate\" value=\"{{ utilisateur.dateNaissance | date('Y-m-d') }}\" placeholder=\"Date de naissance\">
            </div>
            <div class=\"form-group\">
                <label for=\"userMail\">Mail</label>
                <input type=\"email\" name=\"mail\" class=\"form-control\" id=\"userMail\" value=\"{{ utilisateur.mail }}\" placeholder=\"Adresse mail\">
            </div>
            <div class=\"form-group\">
                <label for=\"userPhoneNumber\">Téléphone</label>
                <input type=\"text\" name=\"telephone\" class=\"form-control\" id=\"userPhoneNumber\" value=\"{{ utilisateur.telephone }}\" placeholder=\"Numéro de téléphone\">
            </div>
            <div class=\"form-group\">
                <label for=\"promotionSelect\">Promotions</label>
                <select name=\"promotion\" id=\"promotionSelect\">
                    {% for promotion in promotions %}
                        <option value=\"{{ promotion.idPromotion }}\">{{ promotion.libellePromotion }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class=\"form-group\">
                <label for=\"rolesSelect\">Roles</label>
                <select name=\"role\" id=\"rolesSelect\">
                    {% for role in roles %}
                        <option value=\"{{ role.idRole }}\">{{ role.libelleRole }}</option>
                    {% endfor %}
                </select>
            </div>
            <button type=\"submit\" name=\"id\" class=\"btn btn-primary\" value=\"{{ utilisateur.idUtilisateur }}\">Sauvegarder</button>
            <a href=\"/admin/utilisateurs\" class=\"btn btn-primary\">Annuler</a>
        </form>
    </main>
{% endblock %}
", "admin/utilisateurs/admin_form_edit_utilisateur.html.twig", "C:\\Users\\basil\\Documents\\ESGIcours\\Projet Annuel\\FrameworkERs\\templates\\admin\\utilisateurs\\admin_form_edit_utilisateur.html.twig");
    }
}

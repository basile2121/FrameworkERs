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

/* admin/utilisateurs/admin_utilisateurs.html.twig */
class __TwigTemplate_43a7a406066c3e13dcd0ad461aa8bad0 extends Template
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
        $this->parent = $this->loadTemplate("base.html.twig", "admin/utilisateurs/admin_utilisateurs.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo "Gestions des utilisateurs";
    }

    // line 6
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 7
        echo "    <h1 class=\"display-1 text-center mt-5\">Utilisateurs</h1>
    <main>
        <form class=\"form-admin\" method=\"POST\" action=\"/admin/utilisateurs/filter\">
            <div class=\"form-group\">
                <label for=\"searchUserLastName\">Rechercher un utilisateur par nom</label>
                <input type=\"text\" value=\"";
        // line 12
        echo twig_escape_filter($this->env, (($__internal_compile_0 = ($context["filtres"] ?? null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0["filtre_lastName"] ?? null) : null), "html", null, true);
        echo "\" name=\"filtre_lastName\" class=\"form-control\" id=\"searchUserLastName\" placeholder=\"Nom de l'utilisateur\">
            </div>
            <div class=\"form-group\">
                <label for=\"searchUserFirstName\">Rechercher un utilisateur par son prénom</label>
                <input type=\"text\" value=\"";
        // line 16
        echo twig_escape_filter($this->env, (($__internal_compile_1 = ($context["filtres"] ?? null)) && is_array($__internal_compile_1) || $__internal_compile_1 instanceof ArrayAccess ? ($__internal_compile_1["filtre_firstName"] ?? null) : null), "html", null, true);
        echo "\" name=\"filtre_firstName\" class=\"form-control\" id=\"searchUserFirstName\" placeholder=\"Prénom de l'utilisateur\">
            </div>
            <div class=\"form-group\">
                <label for=\"ecoleSelect\">Ecoles</label>
                <select class=\"form-select\" name=\"filtre_ecole\" id=\"ecoleSelect\">
                    ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["ecoles"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["ecole"]) {
            // line 22
            echo "                        ";
            if ((twig_get_attribute($this->env, $this->source, ($context["filtres"] ?? null), "filtre_ecole", [], "array", true, true, false, 22) && (twig_get_attribute($this->env, $this->source, $context["ecole"], "idEcole", [], "any", false, false, false, 22) == (($__internal_compile_2 = ($context["filtres"] ?? null)) && is_array($__internal_compile_2) || $__internal_compile_2 instanceof ArrayAccess ? ($__internal_compile_2["filtre_ecole"] ?? null) : null)))) {
                // line 23
                echo "                            <option selected value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["ecole"], "idEcole", [], "any", false, false, false, 23), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["ecole"], "nomEcole", [], "any", false, false, false, 23), "html", null, true);
                echo "</option>
                        ";
            } else {
                // line 25
                echo "                            <option value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["ecole"], "idEcole", [], "any", false, false, false, 25), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["ecole"], "nomEcole", [], "any", false, false, false, 25), "html", null, true);
                echo "</option>
                        ";
            }
            // line 27
            echo "                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ecole'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 28
        echo "                </select>
            </div>
            <div class=\"form-group\">
                <label for=\"promotionsSelect\">Promotions</label>
                <select class=\"form-select\" name=\"filtre_promotion\" class=\"form-control\" id=\"promotionsSelect\">
                    ";
        // line 33
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["promotions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["promotion"]) {
            // line 34
            echo "                        ";
            if ((twig_get_attribute($this->env, $this->source, ($context["filtres"] ?? null), "filtre_promotion", [], "array", true, true, false, 34) && (twig_get_attribute($this->env, $this->source, $context["promotion"], "idPromotion", [], "any", false, false, false, 34) == (($__internal_compile_3 = ($context["filtres"] ?? null)) && is_array($__internal_compile_3) || $__internal_compile_3 instanceof ArrayAccess ? ($__internal_compile_3["filtre_promotion"] ?? null) : null)))) {
                // line 35
                echo "                            <option selected value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["promotion"], "idPromotion", [], "any", false, false, false, 35), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["promotion"], "libellePromotion", [], "any", false, false, false, 35), "html", null, true);
                echo "</option>
                        ";
            } else {
                // line 37
                echo "                            <option value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["promotion"], "idPromotion", [], "any", false, false, false, 37), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["promotion"], "libellePromotion", [], "any", false, false, false, 37), "html", null, true);
                echo "</option>
                        ";
            }
            // line 39
            echo "                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['promotion'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 40
        echo "                </select>
            </div>
            <div class=\"form-group\">
                <label for=\"rolesSelect\">Roles</label>
                <select class=\"form-select\" name=\"filtre_role\" id=\"rolesSelect\">
                    ";
        // line 45
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["promotions"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["role"]) {
            // line 46
            echo "                        ";
            if ((twig_get_attribute($this->env, $this->source, ($context["filtres"] ?? null), "filtre_role", [], "array", true, true, false, 46) && (twig_get_attribute($this->env, $this->source, $context["role"], "idRole", [], "any", false, false, false, 46) == (($__internal_compile_4 = ($context["filtres"] ?? null)) && is_array($__internal_compile_4) || $__internal_compile_4 instanceof ArrayAccess ? ($__internal_compile_4["filtre_role"] ?? null) : null)))) {
                // line 47
                echo "                            <option selected value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["role"], "idRole", [], "any", false, false, false, 47), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["role"], "libelleRole", [], "any", false, false, false, 47), "html", null, true);
                echo "</option>
                        ";
            } else {
                // line 49
                echo "                            <option value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["role"], "idRole", [], "any", false, false, false, 49), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["role"], "libelleRole", [], "any", false, false, false, 49), "html", null, true);
                echo "</option>
                        ";
            }
            // line 51
            echo "                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['role'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 52
        echo "                </select>
            </div>
            <button type=\"submit\" name=\"filtre\" value=\"true\" class=\"btn btn-primary\">Rechercher</button>
            <a href=\"/admin/utilisateurs\" class=\"btn btn-primary\">Annuler</a>
        </form>

        <table class=\"table table-striped\">
            <thead>
                <tr>
                    <th scope=\"col\">Nom</th>
                    <th scope=\"col\">Prénom</th>
                    <th scope=\"col\">Date de naissance</th>
                    <th scope=\"col\">Date d'inscription</th>
                    <th scope=\"col\">Mail</th>
                    <th scope=\"col\">Téléphone</th>
                    <th scope=\"col\">Role</th>
                    <th scope=\"col\">Ecole</th>
                    <th scope=\"col\">Actions</th>
                </tr>
            </thead>
            <tbody>
            ";
        // line 73
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["utilisateurs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["utilisateur"]) {
            // line 74
            echo "                <tr>
                    <th scope=\"row\">";
            // line 75
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "nom", [], "any", false, false, false, 75), "html", null, true);
            echo "</th>
                    <td>";
            // line 76
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "prenom", [], "any", false, false, false, 76), "html", null, true);
            echo "</td>
                    <td>";
            // line 77
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "dateNaissance", [], "any", false, false, false, 77), "d-m-Y"), "html", null, true);
            echo "</td>
                    <td>";
            // line 78
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "dateInscription", [], "any", false, false, false, 78), "d-m-Y"), "html", null, true);
            echo "</td>
                    <td>";
            // line 79
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "mail", [], "any", false, false, false, 79), "html", null, true);
            echo "</td>
                    <td>";
            // line 80
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "telephone", [], "any", false, false, false, 80), "html", null, true);
            echo "</td>
                    <td>";
            // line 81
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "roles", [], "any", false, false, false, 81), "libelleRole", [], "any", false, false, false, 81), "html", null, true);
            echo "</td>
                    <td>";
            // line 82
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "nom", [], "any", false, false, false, 82), "html", null, true);
            echo "</td>
                    <td>
                        <form method=\"POST\" action=\"/admin/edit/utilisateurs\" >
                            <button type=\"submit\" name=\"id\" class=\"btn btn-warning\" value=\"";
            // line 85
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "idUtilisateur", [], "any", false, false, false, 85), "html", null, true);
            echo "\">Modifier</button>
                        </form>
                        <form method=\"POST\" action=\"/admin/delete/utilisateurs\" >
                            <button type=\"submit\" name=\"id\" class=\"btn btn-danger\" value=\"";
            // line 88
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["utilisateur"], "idUtilisateur", [], "any", false, false, false, 88), "html", null, true);
            echo "\">Supprimer</button>
                        </form>
                    </td>
                </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['utilisateur'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 93
        echo "            </tbody>
        </table>
    </main>
";
    }

    public function getTemplateName()
    {
        return "admin/utilisateurs/admin_utilisateurs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  262 => 93,  251 => 88,  245 => 85,  239 => 82,  235 => 81,  231 => 80,  227 => 79,  223 => 78,  219 => 77,  215 => 76,  211 => 75,  208 => 74,  204 => 73,  181 => 52,  175 => 51,  167 => 49,  159 => 47,  156 => 46,  152 => 45,  145 => 40,  139 => 39,  131 => 37,  123 => 35,  120 => 34,  116 => 33,  109 => 28,  103 => 27,  95 => 25,  87 => 23,  84 => 22,  80 => 21,  72 => 16,  65 => 12,  58 => 7,  54 => 6,  47 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"base.html.twig\" %}

{% block title %}Gestions des utilisateurs{% endblock %}


{% block content %}
    <h1 class=\"display-1 text-center mt-5\">Utilisateurs</h1>
    <main>
        <form class=\"form-admin\" method=\"POST\" action=\"/admin/utilisateurs/filter\">
            <div class=\"form-group\">
                <label for=\"searchUserLastName\">Rechercher un utilisateur par nom</label>
                <input type=\"text\" value=\"{{ filtres['filtre_lastName'] }}\" name=\"filtre_lastName\" class=\"form-control\" id=\"searchUserLastName\" placeholder=\"Nom de l'utilisateur\">
            </div>
            <div class=\"form-group\">
                <label for=\"searchUserFirstName\">Rechercher un utilisateur par son prénom</label>
                <input type=\"text\" value=\"{{ filtres['filtre_firstName'] }}\" name=\"filtre_firstName\" class=\"form-control\" id=\"searchUserFirstName\" placeholder=\"Prénom de l'utilisateur\">
            </div>
            <div class=\"form-group\">
                <label for=\"ecoleSelect\">Ecoles</label>
                <select class=\"form-select\" name=\"filtre_ecole\" id=\"ecoleSelect\">
                    {% for ecole in ecoles %}
                        {% if filtres['filtre_ecole'] is defined and ecole.idEcole == filtres['filtre_ecole'] %}
                            <option selected value=\"{{ ecole.idEcole }}\">{{ ecole.nomEcole }}</option>
                        {% else %}
                            <option value=\"{{ ecole.idEcole }}\">{{ ecole.nomEcole }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
            <div class=\"form-group\">
                <label for=\"promotionsSelect\">Promotions</label>
                <select class=\"form-select\" name=\"filtre_promotion\" class=\"form-control\" id=\"promotionsSelect\">
                    {% for promotion in promotions %}
                        {% if filtres['filtre_promotion'] is defined and promotion.idPromotion == filtres['filtre_promotion'] %}
                            <option selected value=\"{{ promotion.idPromotion }}\">{{ promotion.libellePromotion }}</option>
                        {% else %}
                            <option value=\"{{ promotion.idPromotion }}\">{{ promotion.libellePromotion }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
            <div class=\"form-group\">
                <label for=\"rolesSelect\">Roles</label>
                <select class=\"form-select\" name=\"filtre_role\" id=\"rolesSelect\">
                    {% for role in promotions %}
                        {% if filtres['filtre_role'] is defined and role.idRole == filtres['filtre_role'] %}
                            <option selected value=\"{{ role.idRole }}\">{{ role.libelleRole }}</option>
                        {% else %}
                            <option value=\"{{ role.idRole }}\">{{ role.libelleRole }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </div>
            <button type=\"submit\" name=\"filtre\" value=\"true\" class=\"btn btn-primary\">Rechercher</button>
            <a href=\"/admin/utilisateurs\" class=\"btn btn-primary\">Annuler</a>
        </form>

        <table class=\"table table-striped\">
            <thead>
                <tr>
                    <th scope=\"col\">Nom</th>
                    <th scope=\"col\">Prénom</th>
                    <th scope=\"col\">Date de naissance</th>
                    <th scope=\"col\">Date d'inscription</th>
                    <th scope=\"col\">Mail</th>
                    <th scope=\"col\">Téléphone</th>
                    <th scope=\"col\">Role</th>
                    <th scope=\"col\">Ecole</th>
                    <th scope=\"col\">Actions</th>
                </tr>
            </thead>
            <tbody>
            {% for utilisateur in utilisateurs %}
                <tr>
                    <th scope=\"row\">{{ utilisateur.nom }}</th>
                    <td>{{ utilisateur.prenom }}</td>
                    <td>{{ utilisateur.dateNaissance | date('d-m-Y') }}</td>
                    <td>{{ utilisateur.dateInscription | date('d-m-Y')}}</td>
                    <td>{{ utilisateur.mail }}</td>
                    <td>{{ utilisateur.telephone }}</td>
                    <td>{{ utilisateur.roles.libelleRole }}</td>
                    <td>{{ utilisateur.nom }}</td>
                    <td>
                        <form method=\"POST\" action=\"/admin/edit/utilisateurs\" >
                            <button type=\"submit\" name=\"id\" class=\"btn btn-warning\" value=\"{{ utilisateur.idUtilisateur}}\">Modifier</button>
                        </form>
                        <form method=\"POST\" action=\"/admin/delete/utilisateurs\" >
                            <button type=\"submit\" name=\"id\" class=\"btn btn-danger\" value=\"{{ utilisateur.idUtilisateur }}\">Supprimer</button>
                        </form>
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </main>
{% endblock %}
", "admin/utilisateurs/admin_utilisateurs.html.twig", "C:\\Users\\basil\\Documents\\ESGIcours\\Projet Annuel\\FrameworkERs\\templates\\admin\\utilisateurs\\admin_utilisateurs.html.twig");
    }
}

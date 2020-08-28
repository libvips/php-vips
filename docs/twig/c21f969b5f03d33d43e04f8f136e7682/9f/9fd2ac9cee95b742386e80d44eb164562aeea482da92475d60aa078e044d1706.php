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

/* property.html.twig */
class __TwigTemplate_0a8a07150e607a207507d314f7088ef897509f5bd3699138605a87e2fc0f9b1b extends \Twig\Template
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
        echo "<a id=\"property_";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "name", [], "any", false, false, false, 1), "html", null, true);
        echo "\"></a>
<article
        class=\"
            phpdocumentor-property
            phpdocumentor-property--";
        // line 5
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "visibility", [], "any", false, false, false, 5), "html", null, true);
        echo "
            ";
        // line 6
        if (twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "static", [], "any", false, false, false, 6)) {
            echo "phpdocumentor-property--static";
        }
        // line 7
        echo "            ";
        if (twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "deprecated", [], "any", false, false, false, 7)) {
            echo "phpdocumentor-element--deprecated";
        }
        // line 8
        echo "        \"
>
    <h4 class=\"phpdocumentor-property__name\">\$";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "name", [], "any", false, false, false, 10), "html", null, true);
        echo "</h4>
    <aside class=\"phpdocumentor-element-found-in\">
        <abbr class=\"phpdocumentor-element-found-in__file\" title=\"";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "file", [], "any", false, false, false, 12), "path", [], "any", false, false, false, 12), "html", null, true);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "file", [], "any", false, false, false, 12), "file:short"]);
        echo "</abbr>
        :
        <span class=\"phpdocumentor-element-found-in__line\">";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "line", [], "any", false, false, false, 14), "html", null, true);
        echo "</span>
    </aside>
    ";
        // line 16
        if (twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "summary", [], "any", false, false, false, 16)) {
            // line 17
            echo "        <p class=\"phpdocumentor-summary\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "summary", [], "any", false, false, false, 17), "html", null, true);
            echo "</p>
    ";
        }
        // line 19
        echo "    <code class=\"phpdocumentor-signature phpdocumentor-code ";
        if (twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "deprecated", [], "any", false, false, false, 19)) {
            echo "phpdocumentor-signature--deprecated";
        }
        echo "\">
        <span class=\"phpdocumentor-signature__visibility\">";
        // line 20
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "visibility", [], "any", false, false, false, 20), "html", null, true);
        echo "</span>
        ";
        // line 21
        if (twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "static", [], "any", false, false, false, 21)) {
            echo "<span class=\"phpdocumentor-signature__static\">static</span>";
        }
        // line 22
        echo "        <span class=\"phpdocumentor-signature__type\">";
        echo (( !twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "type", [], "any", false, false, false, 22)) ? ("mixed") : (twig_join_filter(call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "type", [], "any", false, false, false, 22), "class:short"]), "|")));
        echo "</span>
        <span class=\"phpdocumentor-signature__name\">\$";
        // line 23
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "name", [], "any", false, false, false, 23), "html", null, true);
        echo "</span>
        ";
        // line 24
        if ( !(null === twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "default", [], "any", false, false, false, 24))) {
            echo " = <span class=\"phpdocumentor-signature__default-value\">";
            ((twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "default", [], "any", false, false, false, 24)) ? (print (twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "default", [], "any", false, false, false, 24), "html", null, true))) : (print ("\"\"")));
            echo "</span>";
        }
        // line 25
        echo "    </code>
    ";
        // line 26
        if (twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "description", [], "any", false, false, false, 26)) {
            // line 27
            echo "    <section class=\"phpdocumentor-description\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["property"] ?? null), "description", [], "any", false, false, false, 27), "html", null, true);
            echo "</section>
    ";
        }
        // line 29
        echo "</article>
";
    }

    public function getTemplateName()
    {
        return "property.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  128 => 29,  122 => 27,  120 => 26,  117 => 25,  111 => 24,  107 => 23,  102 => 22,  98 => 21,  94 => 20,  87 => 19,  81 => 17,  79 => 16,  74 => 14,  67 => 12,  62 => 10,  58 => 8,  53 => 7,  49 => 6,  45 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "property.html.twig", "property.html.twig");
    }
}

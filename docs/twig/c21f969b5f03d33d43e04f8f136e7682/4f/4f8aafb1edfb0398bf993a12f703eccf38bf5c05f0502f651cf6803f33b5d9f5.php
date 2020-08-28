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

/* constant.html.twig */
class __TwigTemplate_fe2e6b522994ba1810d35c1db6860166f26f4b696db2ee06001a71a54c08447e extends \Twig\Template
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
        echo "<a id=\"constant_";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "name", [], "any", false, false, false, 1), "html", null, true);
        echo "\"></a>
<article
        class=\"
            phpdocumentor-element
            phpdocumentor-constant
            phpdocumentor-constant--";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "visibility", [], "any", false, false, false, 6), "html", null, true);
        echo "
            ";
        // line 7
        if (twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "deprecated", [], "any", false, false, false, 7)) {
            echo "phpdocumentor-element--deprecated";
        }
        // line 8
        echo "        \"
>
    <h4 class=\"phpdocumentor-constant__name\">";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "name", [], "any", false, false, false, 10), "html", null, true);
        echo "</h4>
    <aside class=\"phpdocumentor-element-found-in\">
        <abbr class=\"phpdocumentor-element-found-in__file\" title=\"";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "file", [], "any", false, false, false, 12), "path", [], "any", false, false, false, 12), "html", null, true);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "file", [], "any", false, false, false, 12), "file:short"]);
        echo "</abbr>
        :
        <span class=\"phpdocumentor-element-found-in__line\">";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "line", [], "any", false, false, false, 14), "html", null, true);
        echo "</span>
    </aside>
    ";
        // line 16
        if (twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "summary", [], "any", false, false, false, 16)) {
            // line 17
            echo "        <p class=\"phpdocumentor-summary\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "summary", [], "any", false, false, false, 17), "html", null, true);
            echo "</p>
    ";
        }
        // line 19
        echo "    <code class=\"phpdocumentor-signature phpdocumentor-code ";
        if (twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "deprecated", [], "any", false, false, false, 19)) {
            echo "phpdocumentor-signature--deprecated";
        }
        echo "\">
        <span class=\"phpdocumentor-signature__visibility\">";
        // line 20
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "visibility", [], "any", false, false, false, 20), "html", null, true);
        echo "</span>
        <span class=\"phpdocumentor-signature__type\">";
        // line 21
        echo (( !twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "type", [], "any", false, false, false, 21)) ? ("mixed") : (twig_join_filter(call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "type", [], "any", false, false, false, 21), "class:short"]), "|")));
        echo "</span>
        <span class=\"phpdocumentor-signature__name\">\$";
        // line 22
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "name", [], "any", false, false, false, 22), "html", null, true);
        echo "</span>
        = <span class=\"phpdocumentor-signature__default-value\">";
        // line 23
        ((twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "value", [], "any", false, false, false, 23)) ? (print (twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "value", [], "any", false, false, false, 23), "html", null, true))) : (print ("\"\"")));
        echo "</span>
    </code>
    ";
        // line 25
        if (twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "description", [], "any", false, false, false, 25)) {
            // line 26
            echo "    <section class=\"phpdocumentor-description\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["constant"] ?? null), "description", [], "any", false, false, false, 26), "html", null, true);
            echo "</section>
    ";
        }
        // line 28
        echo "</article>
";
    }

    public function getTemplateName()
    {
        return "constant.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  115 => 28,  109 => 26,  107 => 25,  102 => 23,  98 => 22,  94 => 21,  90 => 20,  83 => 19,  77 => 17,  75 => 16,  70 => 14,  63 => 12,  58 => 10,  54 => 8,  50 => 7,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "constant.html.twig", "constant.html.twig");
    }
}

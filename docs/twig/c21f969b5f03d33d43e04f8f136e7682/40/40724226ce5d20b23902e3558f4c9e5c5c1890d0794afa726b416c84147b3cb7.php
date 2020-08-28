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

/* breadcrumbs.html.twig */
class __TwigTemplate_12c187565dbb9655d028bd292bf4e86745f585ac5456a5dd028dc3fde7b7f08a extends \Twig\Template
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
        echo "<ul class=\"phpdocumentor-breadcrumbs\">
    <li><a href=\"";
        // line 2
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["/index.html"]), "html", null, true);
        echo "\">Home</a></li>
    ";
        // line 3
        if (($context["usesNamespaces"] ?? null)) {
            // line 4
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(call_user_func_array($this->env->getFunction('breadcrumbs')->getCallable(), [($context["node"] ?? null)]));
            foreach ($context['_seq'] as $context["_key"] => $context["breadcrumb"]) {
                // line 5
                echo "            <li><a href=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('link')->getCallable(), [$context["breadcrumb"]]), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["breadcrumb"], "name", [], "any", false, false, false, 5), "html", null, true);
                echo "</a></li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['breadcrumb'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 7
            echo "    ";
        } else {
            // line 8
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(call_user_func_array($this->env->getFunction('packages')->getCallable(), [($context["node"] ?? null)]));
            foreach ($context['_seq'] as $context["_key"] => $context["breadcrumb"]) {
                // line 9
                echo "            <li><a href=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('link')->getCallable(), [$context["breadcrumb"]]), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["breadcrumb"], "name", [], "any", false, false, false, 9), "html", null, true);
                echo "</a></li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['breadcrumb'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 11
            echo "    ";
        }
        // line 12
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "breadcrumbs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  84 => 12,  81 => 11,  70 => 9,  65 => 8,  62 => 7,  51 => 5,  46 => 4,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "breadcrumbs.html.twig", "breadcrumbs.html.twig");
    }
}

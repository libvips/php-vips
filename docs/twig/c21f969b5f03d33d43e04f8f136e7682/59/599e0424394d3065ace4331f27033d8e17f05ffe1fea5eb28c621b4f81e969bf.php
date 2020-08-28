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

/* /file.html.twig */
class __TwigTemplate_c868781680a63b83ed5d009a0de436cd6f18e967bb533e8412a1f4db39652573 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
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
        $this->parent = $this->loadTemplate("base.html.twig", "/file.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "    ";
        $this->loadTemplate("breadcrumbs.html.twig", "/file.html.twig", 4)->display($context);
        // line 5
        echo "
    <h2>";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "name", [], "any", false, false, false, 6), "html", null, true);
        echo "</h2>

    ";
        // line 8
        if (( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "interfaces", [], "any", false, false, false, 8)) ||  !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "classes", [], "any", false, false, false, 8)))) {
            // line 9
            echo "        <h3>Interfaces, Classes and Traits</h3>
        <dl>
            ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "interfaces", [], "any", false, false, false, 11));
            foreach ($context['_seq'] as $context["_key"] => $context["interface"]) {
                // line 12
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["interface"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 13
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["interface"], "summary", [], "any", false, false, false, 13), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['interface'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 15
            echo "
            ";
            // line 16
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "classes", [], "any", false, false, false, 16));
            foreach ($context['_seq'] as $context["_key"] => $context["class"]) {
                // line 17
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["class"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 18
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["class"], "summary", [], "any", false, false, false, 18), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 20
            echo "
            ";
            // line 21
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "traits", [], "any", false, false, false, 21));
            foreach ($context['_seq'] as $context["_key"] => $context["trait"]) {
                // line 22
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["trait"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 23
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["trait"], "summary", [], "any", false, false, false, 23), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['trait'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 25
            echo "        </dl>
    ";
        }
        // line 27
        echo "
    ";
        // line 28
        if (( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 28)) ||  !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 28)))) {
            // line 29
            echo "    <h3>Table of Contents</h3>
    <table class=\"phpdocumentor-table_of_contents\">
        ";
            // line 31
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 31));
            foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
                // line 32
                echo "            <tr>
                <th class=\"phpdocumentor-heading\">";
                // line 33
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["constant"], "class:short"]);
                echo "</th>
                <td class=\"phpdocumentor-cell\">";
                // line 34
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "summary", [], "any", false, false, false, 34), "html", null, true);
                echo "</td>
                <td class=\"phpdocumentor-cell\">";
                // line 35
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "value", [], "any", false, false, false, 35), "html", null, true);
                echo "</td>
            </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 38
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 38));
            foreach ($context['_seq'] as $context["_key"] => $context["function"]) {
                // line 39
                echo "            <tr>
                <th class=\"phpdocumentor-heading\">";
                // line 40
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["function"], "class:short"]);
                echo "</th>
                <td class=\"phpdocumentor-cell\">";
                // line 41
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["function"], "summary", [], "any", false, false, false, 41), "html", null, true);
                echo "</td>
                <td class=\"phpdocumentor-cell\">";
                // line 42
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["function"], "type", [], "any", false, false, false, 42), twig_join_filter("class:short", "|")]);
                echo "</td>
            </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['function'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 45
            echo "    </table>
    ";
        }
        // line 47
        echo "
    ";
        // line 48
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 48))) {
            // line 49
            echo "        <h3>Constants</h3>
        <ul>
            ";
            // line 51
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 51));
            foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
                // line 52
                echo "                <li><a href=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('link')->getCallable(), [$context["constant"]]), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "name", [], "any", false, false, false, 52), "html", null, true);
                echo "</a></li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 54
            echo "        </ul>
    ";
        }
        // line 56
        echo "
    ";
        // line 57
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 57))) {
            // line 58
            echo "        <section>
            <h3 class=\"phpdocumentor-functions__header\">Functions</h3>
            ";
            // line 60
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 60));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["function"]) {
                // line 61
                echo "                ";
                $this->loadTemplate("function.html.twig", "/file.html.twig", 61)->display($context);
                // line 62
                echo "            ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['function'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 63
            echo "        </section>
    ";
        }
    }

    public function getTemplateName()
    {
        return "/file.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  261 => 63,  247 => 62,  244 => 61,  227 => 60,  223 => 58,  221 => 57,  218 => 56,  214 => 54,  203 => 52,  199 => 51,  195 => 49,  193 => 48,  190 => 47,  186 => 45,  177 => 42,  173 => 41,  169 => 40,  166 => 39,  161 => 38,  152 => 35,  148 => 34,  144 => 33,  141 => 32,  137 => 31,  133 => 29,  131 => 28,  128 => 27,  124 => 25,  116 => 23,  111 => 22,  107 => 21,  104 => 20,  96 => 18,  91 => 17,  87 => 16,  84 => 15,  76 => 13,  71 => 12,  67 => 11,  63 => 9,  61 => 8,  56 => 6,  53 => 5,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "/file.html.twig", "/file.html.twig");
    }
}

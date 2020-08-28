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

/* /index.html.twig */
class __TwigTemplate_740c23191b37626011375dfd0272c1b0980038004eca94a27f3c0cf846ea0b29 extends \Twig\Template
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
        $this->parent = $this->loadTemplate("base.html.twig", "/index.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "    <h2>Documentation</h2>

    ";
        // line 6
        if ((($context["usesNamespaces"] ?? null) ||  !($context["usePackages"] ?? null))) {
            // line 7
            echo "        <h3>Namespaces</h3>
        <dl>
            ";
            // line 9
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 9), "children", [], "any", false, false, false, 9));
            foreach ($context['_seq'] as $context["_key"] => $context["namespace"]) {
                // line 10
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["namespace"], "class:short"]);
                echo "</dt>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['namespace'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            echo "        </dl>
    ";
        }
        // line 14
        echo "
    ";
        // line 15
        if (($context["usesPackages"] ?? null)) {
            // line 16
            echo "        <h3>Packages</h3>
        <dl>
            ";
            // line 18
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "package", [], "any", false, false, false, 18), "children", [], "any", false, false, false, 18));
            foreach ($context['_seq'] as $context["_key"] => $context["package"]) {
                // line 19
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["package"], "class:short"]);
                echo "</dt>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['package'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "        </dl>
    ";
        }
        // line 23
        echo "
    ";
        // line 24
        if (( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 24), "interfaces", [], "any", false, false, false, 24)) ||  !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 24), "classes", [], "any", false, false, false, 24)))) {
            // line 25
            echo "        <h3>Interfaces, Classes and Traits</h3>
        <dl>
            ";
            // line 27
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 27), "interfaces", [], "any", false, false, false, 27));
            foreach ($context['_seq'] as $context["_key"] => $context["interface"]) {
                // line 28
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["interface"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 29
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["interface"], "summary", [], "any", false, false, false, 29), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['interface'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 31
            echo "
            ";
            // line 32
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 32), "classes", [], "any", false, false, false, 32));
            foreach ($context['_seq'] as $context["_key"] => $context["class"]) {
                // line 33
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["class"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 34
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["class"], "summary", [], "any", false, false, false, 34), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 36
            echo "
            ";
            // line 37
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 37), "traits", [], "any", false, false, false, 37));
            foreach ($context['_seq'] as $context["_key"] => $context["trait"]) {
                // line 38
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["trait"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 39
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["trait"], "summary", [], "any", false, false, false, 39), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['trait'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 41
            echo "        </dl>
    ";
        }
        // line 43
        echo "
    ";
        // line 44
        if (( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 44), "constants", [], "any", false, false, false, 44)) ||  !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 44), "functions", [], "any", false, false, false, 44)))) {
            // line 45
            echo "    <h3>Table of Contents</h3>
    <table class=\"phpdocumentor-table_of_contents\">
        ";
            // line 47
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 47), "constants", [], "any", false, false, false, 47));
            foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
                // line 48
                echo "            <tr>
                <th class=\"phpdocumentor-heading\">";
                // line 49
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["constant"], "class:short"]);
                echo "</th>
                <td class=\"phpdocumentor-cell\">";
                // line 50
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "summary", [], "any", false, false, false, 50), "html", null, true);
                echo "</td>
                <td class=\"phpdocumentor-cell\">";
                // line 51
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "value", [], "any", false, false, false, 51), "html", null, true);
                echo "</td>
            </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 54
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 54), "functions", [], "any", false, false, false, 54));
            foreach ($context['_seq'] as $context["_key"] => $context["function"]) {
                // line 55
                echo "            <tr>
                <th class=\"phpdocumentor-heading\">";
                // line 56
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["function"], "class:short"]);
                echo "</th>
                <td class=\"phpdocumentor-cell\">";
                // line 57
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["function"], "summary", [], "any", false, false, false, 57), "html", null, true);
                echo "</td>
                <td class=\"phpdocumentor-cell\">";
                // line 58
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["function"], "type", [], "any", false, false, false, 58), twig_join_filter("class:short", "|")]);
                echo "</td>
            </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['function'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 61
            echo "    </table>
    ";
        }
        // line 63
        echo "
    ";
        // line 64
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 64), "constants", [], "any", false, false, false, 64))) {
            // line 65
            echo "        <h3>Constants</h3>
        <ul>
            ";
            // line 67
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 67), "constants", [], "any", false, false, false, 67));
            foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
                // line 68
                echo "                <li><a href=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('link')->getCallable(), [$context["constant"]]), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "name", [], "any", false, false, false, 68), "html", null, true);
                echo "</a></li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 70
            echo "        </ul>
    ";
        }
        // line 72
        echo "
    ";
        // line 73
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 73), "functions", [], "any", false, false, false, 73))) {
            // line 74
            echo "        <section>
            <h3 class=\"phpdocumentor-functions__header\">Functions</h3>
            ";
            // line 76
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 76), "functions", [], "any", false, false, false, 76));
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
                // line 77
                echo "                ";
                $this->loadTemplate("function.html.twig", "/index.html.twig", 77)->display($context);
                // line 78
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
            // line 79
            echo "        </section>
    ";
        }
    }

    public function getTemplateName()
    {
        return "/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  306 => 79,  292 => 78,  289 => 77,  272 => 76,  268 => 74,  266 => 73,  263 => 72,  259 => 70,  248 => 68,  244 => 67,  240 => 65,  238 => 64,  235 => 63,  231 => 61,  222 => 58,  218 => 57,  214 => 56,  211 => 55,  206 => 54,  197 => 51,  193 => 50,  189 => 49,  186 => 48,  182 => 47,  178 => 45,  176 => 44,  173 => 43,  169 => 41,  161 => 39,  156 => 38,  152 => 37,  149 => 36,  141 => 34,  136 => 33,  132 => 32,  129 => 31,  121 => 29,  116 => 28,  112 => 27,  108 => 25,  106 => 24,  103 => 23,  99 => 21,  90 => 19,  86 => 18,  82 => 16,  80 => 15,  77 => 14,  73 => 12,  64 => 10,  60 => 9,  56 => 7,  54 => 6,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "/index.html.twig", "/index.html.twig");
    }
}

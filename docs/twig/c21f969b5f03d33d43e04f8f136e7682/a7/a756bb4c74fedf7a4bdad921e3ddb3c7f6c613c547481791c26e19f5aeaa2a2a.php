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

/* /package.html.twig */
class __TwigTemplate_1d4b3a3844be30abdc938b871933ec3f65d6ec861403586e6159003b12f30284 extends \Twig\Template
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
        $this->parent = $this->loadTemplate("base.html.twig", "/package.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        echo "    ";
        $this->loadTemplate("breadcrumbs.html.twig", "/package.html.twig", 4)->display($context);
        // line 5
        echo "
    <h2>";
        // line 6
        (((twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "name", [], "any", false, false, false, 6) == "\\")) ? (print ("API Documentation")) : (print (twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "name", [], "any", false, false, false, 6), "html", null, true))));
        echo "</h2>

    ";
        // line 8
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "children", [], "any", false, false, false, 8))) {
            // line 9
            echo "        <h3>Packages</h3>
        <dl>
            ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "children", [], "any", false, false, false, 11));
            foreach ($context['_seq'] as $context["_key"] => $context["package"]) {
                // line 12
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["package"], "class:short"]);
                echo "</dt>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['package'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 14
            echo "        </dl>
    ";
        }
        // line 16
        echo "
    ";
        // line 17
        if (( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "interfaces", [], "any", false, false, false, 17)) ||  !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "classes", [], "any", false, false, false, 17)))) {
            // line 18
            echo "        <h3>Interfaces, Classes and Traits</h3>
        <dl>
            ";
            // line 20
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "interfaces", [], "any", false, false, false, 20));
            foreach ($context['_seq'] as $context["_key"] => $context["interface"]) {
                // line 21
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["interface"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 22
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["interface"], "summary", [], "any", false, false, false, 22), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['interface'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 24
            echo "
            ";
            // line 25
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "classes", [], "any", false, false, false, 25));
            foreach ($context['_seq'] as $context["_key"] => $context["class"]) {
                // line 26
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["class"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 27
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["class"], "summary", [], "any", false, false, false, 27), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 29
            echo "
            ";
            // line 30
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "traits", [], "any", false, false, false, 30));
            foreach ($context['_seq'] as $context["_key"] => $context["trait"]) {
                // line 31
                echo "                <dt>";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["trait"], "class:short"]);
                echo "</dt>
                <dd>";
                // line 32
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["trait"], "summary", [], "any", false, false, false, 32), "html", null, true);
                echo "</dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['trait'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 34
            echo "        </dl>
    ";
        }
        // line 36
        echo "
    ";
        // line 37
        if (( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 37)) ||  !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 37)))) {
            // line 38
            echo "    <h3>Table of Contents</h3>
    <table class=\"phpdocumentor-table_of_contents\">
        ";
            // line 40
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 40));
            foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
                // line 41
                echo "            <tr>
                <th class=\"phpdocumentor-heading\">";
                // line 42
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["constant"], "class:short"]);
                echo "</th>
                <td class=\"phpdocumentor-cell\">";
                // line 43
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "summary", [], "any", false, false, false, 43), "html", null, true);
                echo "</td>
                <td class=\"phpdocumentor-cell\">";
                // line 44
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "value", [], "any", false, false, false, 44), "html", null, true);
                echo "</td>
            </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 47
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 47));
            foreach ($context['_seq'] as $context["_key"] => $context["function"]) {
                // line 48
                echo "            <tr>
                <th class=\"phpdocumentor-heading\">";
                // line 49
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["function"], "class:short"]);
                echo "</th>
                <td class=\"phpdocumentor-cell\">";
                // line 50
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["function"], "summary", [], "any", false, false, false, 50), "html", null, true);
                echo "</td>
                <td class=\"phpdocumentor-cell\">";
                // line 51
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["function"], "type", [], "any", false, false, false, 51), twig_join_filter("class:short", "|")]);
                echo "</td>
            </tr>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['function'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 54
            echo "    </table>
    ";
        }
        // line 56
        echo "
    ";
        // line 57
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 57))) {
            // line 58
            echo "        <h3>Constants</h3>
        <ul>
            ";
            // line 60
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "constants", [], "any", false, false, false, 60));
            foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
                // line 61
                echo "                <li><a href=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('link')->getCallable(), [$context["constant"]]), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["constant"], "name", [], "any", false, false, false, 61), "html", null, true);
                echo "</a></li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 63
            echo "        </ul>
    ";
        }
        // line 65
        echo "
    ";
        // line 66
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 66))) {
            // line 67
            echo "        <section>
            <h3 class=\"phpdocumentor-functions__header\">Functions</h3>
            ";
            // line 69
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["node"] ?? null), "functions", [], "any", false, false, false, 69));
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
                // line 70
                echo "                ";
                $this->loadTemplate("function.html.twig", "/package.html.twig", 70)->display($context);
                // line 71
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
            // line 72
            echo "        </section>
    ";
        }
    }

    public function getTemplateName()
    {
        return "/package.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  287 => 72,  273 => 71,  270 => 70,  253 => 69,  249 => 67,  247 => 66,  244 => 65,  240 => 63,  229 => 61,  225 => 60,  221 => 58,  219 => 57,  216 => 56,  212 => 54,  203 => 51,  199 => 50,  195 => 49,  192 => 48,  187 => 47,  178 => 44,  174 => 43,  170 => 42,  167 => 41,  163 => 40,  159 => 38,  157 => 37,  154 => 36,  150 => 34,  142 => 32,  137 => 31,  133 => 30,  130 => 29,  122 => 27,  117 => 26,  113 => 25,  110 => 24,  102 => 22,  97 => 21,  93 => 20,  89 => 18,  87 => 17,  84 => 16,  80 => 14,  71 => 12,  67 => 11,  63 => 9,  61 => 8,  56 => 6,  53 => 5,  50 => 4,  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "/package.html.twig", "/package.html.twig");
    }
}

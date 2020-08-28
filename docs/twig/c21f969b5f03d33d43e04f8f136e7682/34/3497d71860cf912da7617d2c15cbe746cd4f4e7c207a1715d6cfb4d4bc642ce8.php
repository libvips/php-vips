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

/* method.html.twig */
class __TwigTemplate_795f25fe7f1746696d4888f52f73d556db65475e9503189ef2791ab96c28dc38 extends \Twig\Template
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
        echo "<a id=\"method_";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "name", [], "any", false, false, false, 1), "html", null, true);
        echo "\"></a>
<article
        class=\"
            phpdocumentor-method
            phpdocumentor-method--";
        // line 5
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "visibility", [], "any", false, false, false, 5), "html", null, true);
        echo "
            ";
        // line 6
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "deprecated", [], "any", false, false, false, 6)) {
            echo "phpdocumentor-element--deprecated";
        }
        // line 7
        echo "            ";
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "abstract", [], "any", false, false, false, 7)) {
            echo "phpdocumentor-element--abstract";
        }
        // line 8
        echo "            ";
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "static", [], "any", false, false, false, 8)) {
            echo "phpdocumentor-element--static";
        }
        // line 9
        echo "            ";
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "final", [], "any", false, false, false, 9)) {
            echo "phpdocumentor-element--final";
        }
        // line 10
        echo "        \"
>
    <h4 class=\"phpdocumentor-method__name\">";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "name", [], "any", false, false, false, 12), "html", null, true);
        echo "()</h4>
    <aside class=\"phpdocumentor-element-found-in\">
        <abbr class=\"phpdocumentor-element-found-in__file\" title=\"";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "file", [], "any", false, false, false, 14), "path", [], "any", false, false, false, 14), "html", null, true);
        echo "\">";
        echo call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "file", [], "any", false, false, false, 14), "file:short"]);
        echo "</abbr>
        :
        <span class=\"phpdocumentor-element-found-in__line\">";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "line", [], "any", false, false, false, 16), "html", null, true);
        echo "</span>
    </aside>
    ";
        // line 18
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "summary", [], "any", false, false, false, 18)) {
            // line 19
            echo "    <p class=\"phpdocumentor-summary\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "summary", [], "any", false, false, false, 19), "html", null, true);
            echo "</p>
    ";
        }
        // line 21
        echo "    <code class=\"phpdocumentor-signature phpdocumentor-code ";
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "deprecated", [], "any", false, false, false, 21)) {
            echo "phpdocumentor-signature--deprecated";
        }
        echo "\">
        <span class=\"phpdocumentor-signature__visibility\">";
        // line 22
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "visibility", [], "any", false, false, false, 22), "html", null, true);
        echo "</span>
        ";
        // line 23
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "abstract", [], "any", false, false, false, 23)) {
            echo "<span class=\"phpdocumentor-signature__abstract\">abstract</span>";
        }
        // line 24
        echo "        ";
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "final", [], "any", false, false, false, 24)) {
            echo "<span class=\"phpdocumentor-signature__final\">final</span>";
        }
        // line 25
        echo "        ";
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "static", [], "any", false, false, false, 25)) {
            echo "<span class=\"phpdocumentor-signature__static\">static</span>";
        }
        // line 26
        echo "        <span class=\"phpdocumentor-signature__name\">";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "name", [], "any", false, false, false, 26), "html", null, true);
        echo "</span>(
        ";
        // line 27
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "arguments", [], "any", false, false, false, 27));
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
        foreach ($context['_seq'] as $context["_key"] => $context["argument"]) {
            // line 28
            echo "            <span class=\"phpdocumentor-signature__argument\">
            ";
            // line 29
            if (twig_get_attribute($this->env, $this->source, $context["argument"], "default", [], "any", false, false, false, 29)) {
                echo "[";
            }
            if ( !twig_get_attribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, false, 29)) {
                echo ", ";
            }
            // line 30
            echo "                ";
            if (twig_get_attribute($this->env, $this->source, $context["argument"], "isVariadic", [], "any", false, false, false, 30)) {
                echo "<span class=\"phpdocumentor-signature__argument__variadic-operator\">...</span>";
            }
            // line 31
            if (twig_get_attribute($this->env, $this->source, $context["argument"], "byReference", [], "any", false, false, false, 31)) {
                echo "<span class=\"phpdocumentor-signature__argument__reference-operator\">&amp;</span>";
            }
            // line 32
            echo "<span class=\"phpdocumentor-signature__argument__name\">\$";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["argument"], "name", [], "any", false, false, false, 32), "html", null, true);
            echo "</span> :
                <span class=\"phpdocumentor-signature__argument__return-type\">";
            // line 33
            echo twig_join_filter(call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["argument"], "type", [], "any", false, false, false, 33), "class:short"]), "|");
            echo "</span>
                ";
            // line 34
            if (twig_get_attribute($this->env, $this->source, $context["argument"], "default", [], "any", false, false, false, 34)) {
                echo " = <span class=\"phpdocumentor-signature__argument__default-value\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["argument"], "default", [], "any", false, false, false, 34), "html", null, true);
                echo "</span> ]";
            }
            // line 35
            echo "            </span>
        ";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['argument'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        echo "        )
        : <span class=\"phpdocumentor-signature__response_type\">";
        // line 38
        echo twig_join_filter(call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "response", [], "any", false, false, false, 38), "type", [], "any", false, false, false, 38), "class:short"]), "|");
        echo "</span>
    </code>

    ";
        // line 41
        if (twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "description", [], "any", false, false, false, 41)) {
            // line 42
            echo "    <section class=\"phpdocumentor-description\">";
            echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "description", [], "any", false, false, false, 42)]);
            echo "</section>
    ";
        }
        // line 44
        echo "
    ";
        // line 45
        if ((twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "arguments", [], "any", false, false, false, 45)) > 0)) {
            // line 46
            echo "        <h5 class=\"phpdocumentor-argument-list__heading\">Parameters</h5>
        <dl class=\"phpdocumentor-argument-list\">
            ";
            // line 48
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "arguments", [], "any", false, false, false, 48));
            foreach ($context['_seq'] as $context["_key"] => $context["argument"]) {
                // line 49
                echo "                <dt class=\"phpdocumentor-argument-list__entry\">
                    <span class=\"phpdocumentor-signature__argument__name\">\$";
                // line 50
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["argument"], "name", [], "any", false, false, false, 50), "html", null, true);
                echo "</span>
                    : <span class=\"phpdocumentor-signature__argument__return-type\">";
                // line 51
                echo twig_join_filter(call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["argument"], "type", [], "any", false, false, false, 51), "class:short"]), "|");
                echo "</span>
                    ";
                // line 52
                if (twig_get_attribute($this->env, $this->source, $context["argument"], "default", [], "any", false, false, false, 52)) {
                    echo " = <span class=\"phpdocumentor-signature__argument__default-value\">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["argument"], "default", [], "any", false, false, false, 52), "html", null, true);
                    echo "</span>";
                }
                // line 53
                echo "                </dt>
                <dd class=\"phpdocumentor-argument-list__definition\"><section class=\"phpdocumentor-description\">";
                // line 54
                echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), [((twig_get_attribute($this->env, $this->source, $context["argument"], "description", [], "any", true, true, false, 54)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, $context["argument"], "description", [], "any", false, false, false, 54), "")) : (""))]);
                echo "</section></dd>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['argument'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 56
            echo "        </dl>
    ";
        }
        // line 58
        echo "
    ";
        // line 59
        $context["visibleTags"] = twig_array_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "tags", [], "any", false, false, false, 59), function ($__v__, $__k__) use ($context, $macros) { $context["v"] = $__v__; $context["k"] = $__k__; return !twig_in_filter(($context["k"] ?? null), [0 => "param", 1 => "return"]); });
        // line 60
        echo "    ";
        if ((twig_length_filter($this->env, ($context["visibleTags"] ?? null)) > 0)) {
            // line 61
            echo "        <h5 class=\"phpdocumentor-tag-list__heading\">Tags</h5>
        <dl class=\"phpdocumentor-tag-list\">
            ";
            // line 63
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["visibleTags"] ?? null));
            foreach ($context['_seq'] as $context["name"] => $context["seriesOfTag"]) {
                // line 64
                echo "                ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($context["seriesOfTag"]);
                foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
                    // line 65
                    echo "                    <dt class=\"phpdocumentor-tag-list__entry\">
                        <span class=\"phpdocumentor-tag__name\">";
                    // line 66
                    echo twig_escape_filter($this->env, $context["name"], "html", null, true);
                    echo "</span>
                    </dt>
                    <dd class=\"phpdocumentor-tag-list__definition\"><section class=\"phpdocumentor-description\">";
                    // line 68
                    echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), [((twig_get_attribute($this->env, $this->source, $context["tag"], "description", [], "any", true, true, false, 68)) ? (_twig_default_filter(twig_get_attribute($this->env, $this->source, $context["tag"], "description", [], "any", false, false, false, 68), "")) : (""))]);
                    echo "</section></dd>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tag'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 70
                echo "            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['name'], $context['seriesOfTag'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 71
            echo "        </dl>
    ";
        }
        // line 73
        echo "
    ";
        // line 74
        if (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "response", [], "any", false, false, false, 74), "type", [], "any", false, false, false, 74) && (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "response", [], "any", false, false, false, 74), "type", [], "any", false, false, false, 74) != "void")) || twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "response", [], "any", false, false, false, 74), "description", [], "any", false, false, false, 74))) {
            // line 75
            echo "        <h5 class=\"phpdocumentor-return-value__heading\">Return values</h5>
        <span class=\"phpdocumentor-signature__response_type\">";
            // line 76
            echo twig_join_filter(call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "response", [], "any", false, false, false, 76), "type", [], "any", false, false, false, 76), "class:short"]), "|");
            echo "</span>
        ";
            // line 77
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "response", [], "any", false, false, false, 77), "description", [], "any", false, false, false, 77)) {
                // line 78
                echo "            &mdash; <span class=\"phpdocumentor-description\">";
                echo call_user_func_array($this->env->getFilter('markdown')->getCallable(), [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["method"] ?? null), "response", [], "any", false, false, false, 78), "description", [], "any", false, false, false, 78)]);
                echo "</span>
        ";
            }
            // line 80
            echo "    ";
        }
        // line 81
        echo "
</article>
";
    }

    public function getTemplateName()
    {
        return "method.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  325 => 81,  322 => 80,  316 => 78,  314 => 77,  310 => 76,  307 => 75,  305 => 74,  302 => 73,  298 => 71,  292 => 70,  284 => 68,  279 => 66,  276 => 65,  271 => 64,  267 => 63,  263 => 61,  260 => 60,  258 => 59,  255 => 58,  251 => 56,  243 => 54,  240 => 53,  234 => 52,  230 => 51,  226 => 50,  223 => 49,  219 => 48,  215 => 46,  213 => 45,  210 => 44,  204 => 42,  202 => 41,  196 => 38,  193 => 37,  178 => 35,  172 => 34,  168 => 33,  163 => 32,  159 => 31,  154 => 30,  147 => 29,  144 => 28,  127 => 27,  122 => 26,  117 => 25,  112 => 24,  108 => 23,  104 => 22,  97 => 21,  91 => 19,  89 => 18,  84 => 16,  77 => 14,  72 => 12,  68 => 10,  63 => 9,  58 => 8,  53 => 7,  49 => 6,  45 => 5,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "method.html.twig", "method.html.twig");
    }
}

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

/* /reports/deprecated.html.twig */
class __TwigTemplate_6151b546327a90b950a2f72d697bbe15505b842a86795a87ddee31987e287db8 extends \Twig\Template
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
        // line 3
        $context["deprecatedElements"] = twig_array_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "indexes", [], "any", false, false, false, 3), "elements", [], "any", false, false, false, 3), function ($__element__) use ($context, $macros) { $context["element"] = $__element__; return twig_get_attribute($this->env, $this->source, ($context["element"] ?? null), "deprecated", [], "any", false, false, false, 3); });
        // line 5
        $context["filesWithDeprecatedElements"] = twig_array_reduce($this->env, ($context["deprecatedElements"] ?? null),         // line 6
function ($__unique__, $__item__) use ($context, $macros) { $context["unique"] = $__unique__; $context["item"] = $__item__; return ((twig_in_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "file", [], "any", false, false, false, 6), "path", [], "any", false, false, false, 6), twig_get_array_keys_filter(($context["unique"] ?? null)))) ? (($context["unique"] ?? null)) : (twig_array_merge(($context["unique"] ?? null), [twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "file", [], "any", false, false, false, 6), "path", [], "any", false, false, false, 6) => twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "file", [], "any", false, false, false, 6)]))); }, []);
        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "/reports/deprecated.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 10
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 11
        echo "    ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "name", [], "any", false, false, false, 11), "html", null, true);
        echo " &raquo; Deprecated elements
";
    }

    // line 14
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 15
        echo "    <ul class=\"phpdocumentor-breadcrumbs\">
        <li><a href=\"";
        // line 16
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["/index.html"]), "html", null, true);
        echo "\">Home</a></li>
    </ul>

    <div class=\"phpdocumentor-row\">
        <h2 class=\"phpdocumentor-content__title\">Deprecated</h2>

        ";
        // line 22
        if ( !twig_test_empty(($context["filesWithDeprecatedElements"] ?? null))) {
            // line 23
            echo "        <h3>Table of Contents</h3>
        <table class=\"phpdocumentor-table_of_contents\">
            ";
            // line 25
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["filesWithDeprecatedElements"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                // line 26
                echo "                <tr>
                    <td class=\"phpdocumentor-cell\"><a href=\"#";
                // line 27
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 27), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 27), "html", null, true);
                echo "</a></td>
                </tr>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 30
            echo "        </table>
        ";
        }
        // line 32
        echo "
        ";
        // line 33
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["filesWithDeprecatedElements"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
            // line 34
            echo "            <a id=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 34), "html", null, true);
            echo "\"></a>
            <h3><abbr title=\"";
            // line 35
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 35), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "name", [], "any", false, false, false, 35), "html", null, true);
            echo "</abbr></h3>
            <table>
                <tr>
                    <th class=\"phpdocumentor-heading\">Line</th>
                    <th class=\"phpdocumentor-heading\">Element</th>
                    <th class=\"phpdocumentor-heading\">Reason</th>
                </tr>
                ";
            // line 42
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_array_filter($this->env, ($context["deprecatedElements"] ?? null), function ($__el__) use ($context, $macros) { $context["el"] = $__el__; return (twig_get_attribute($this->env, $this->source, ($context["el"] ?? null), "file", [], "any", false, false, false, 42) == $context["file"]); }));
            foreach ($context['_seq'] as $context["_key"] => $context["element"]) {
                // line 43
                echo "                    ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["element"], "tags", [], "any", false, false, false, 43), "deprecated", [], "any", false, false, false, 43));
                foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
                    // line 44
                    echo "                        <tr>
                            <td class=\"phpdocumentor-cell\">";
                    // line 45
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["element"], "line", [], "any", false, false, false, 45), "html", null, true);
                    echo "</td>
                            <td class=\"phpdocumentor-cell\">";
                    // line 46
                    echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["element"]]);
                    echo "</td>
                            <td class=\"phpdocumentor-cell\">";
                    // line 47
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["tag"], "description", [], "any", false, false, false, 47), "html", null, true);
                    echo "</td>
                        </tr>
                    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tag'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 50
                echo "                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['element'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 51
            echo "            </table>
        ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 53
            echo "            <div class=\"phpdocumentor-admonition phpdocumentor-admonition--success\">
                No deprecated elements have been found in this project.
            </div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 57
        echo "    </div>
";
    }

    public function getTemplateName()
    {
        return "/reports/deprecated.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  183 => 57,  174 => 53,  168 => 51,  162 => 50,  153 => 47,  149 => 46,  145 => 45,  142 => 44,  137 => 43,  133 => 42,  121 => 35,  116 => 34,  111 => 33,  108 => 32,  104 => 30,  93 => 27,  90 => 26,  86 => 25,  82 => 23,  80 => 22,  71 => 16,  68 => 15,  64 => 14,  57 => 11,  53 => 10,  48 => 1,  46 => 6,  45 => 5,  43 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "/reports/deprecated.html.twig", "/reports/deprecated.html.twig");
    }
}

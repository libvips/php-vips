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

/* /reports/errors.html.twig */
class __TwigTemplate_17f05ccf63f92a4d0e3e33ee811bc5eb9737cf0b91775e46c69d3bda5abf4de7 extends \Twig\Template
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
        $context["filesWithErrors"] = twig_array_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "files", [], "any", false, false, false, 3), function ($__file__) use ($context, $macros) { $context["file"] = $__file__; return  !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["file"] ?? null), "allErrors", [], "any", false, false, false, 3)); });
        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "/reports/errors.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 6
        echo "    ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "name", [], "any", false, false, false, 6), "html", null, true);
        echo " &raquo; Compilation errors
";
    }

    // line 9
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 10
        echo "<ul class=\"phpdocumentor-breadcrumbs\">
    <li><a href=\"";
        // line 11
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["/index.html"]), "html", null, true);
        echo "\">Home</a></li>
</ul>

<div class=\"phpdocumentor-row\">
    <h2 class=\"phpdocumentor-content__title\">Errors</h2>

    ";
        // line 17
        if ( !twig_test_empty(($context["filesWithErrors"] ?? null))) {
            // line 18
            echo "    <h3>Table of Contents</h3>
    <table class=\"phpdocumentor-table_of_contents\">
        ";
            // line 20
            $context["errorCount"] = 0;
            // line 21
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_array_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "files", [], "any", false, false, false, 21), function ($__file__) use ($context, $macros) { $context["file"] = $__file__; return  !twig_test_empty(twig_get_attribute($this->env, $this->source, $context["file"], "allErrors", [], "any", false, false, false, 21)); }));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                // line 22
                echo "            ";
                if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["file"], "allerrors", [], "any", false, false, false, 22), "count", [], "any", false, false, false, 22) > 0)) {
                    // line 23
                    echo "                <tr>
                    <td class=\"phpdocumentor-cell\"><a href=\"#";
                    // line 24
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 24), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 24), "html", null, true);
                    echo "</a></td>
                    <td class=\"phpdocumentor-cell\">";
                    // line 25
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["file"], "allErrors", [], "any", false, false, false, 25), "count", [], "any", false, false, false, 25), "html", null, true);
                    echo "</td>
                </tr>
            ";
                }
                // line 28
                echo "            ";
                $context["errorCount"] = (($context["errorCount"] ?? null) + twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["file"], "allerrors", [], "any", false, false, false, 28), "count", [], "any", false, false, false, 28));
                // line 29
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 30
            echo "    </table>
    ";
        }
        // line 32
        echo "
    ";
        // line 33
        if ((($context["errorCount"] ?? null) <= 0)) {
            // line 34
            echo "        <div class=\"phpdocumentor-admonition phpdocumentor-admonition--success\">No errors have been found in this project.</div>
    ";
        }
        // line 36
        echo "
    ";
        // line 37
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["filesWithErrors"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
            // line 38
            echo "        <a id=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 38), "html", null, true);
            echo "\"></a>
        <h3><abbr title=\"";
            // line 39
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 39), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "name", [], "any", false, false, false, 39), "html", null, true);
            echo "</abbr></h3>
        <table>
            <thead>
                <tr>
                    <th class=\"phpdocumentor-heading\">Type</th>
                    <th class=\"phpdocumentor-heading\">Line</th>
                    <th class=\"phpdocumentor-heading\">Description</th>
                </tr>
            </thead>
            <tbody>
            ";
            // line 49
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["file"], "allerrors", [], "any", false, false, false, 49));
            foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                // line 50
                echo "                <tr>
                    <td class=\"phpdocumentor-cell\">";
                // line 51
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["error"], "severity", [], "any", false, false, false, 51), "html", null, true);
                echo "</td>
                    <td class=\"phpdocumentor-cell\">";
                // line 52
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["error"], "line", [], "any", false, false, false, 52), "html", null, true);
                echo "</td>
                    <td class=\"phpdocumentor-cell\">";
                // line 53
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('trans')->getCallable(), [twig_get_attribute($this->env, $this->source, $context["error"], "code", [], "any", false, false, false, 53), twig_get_attribute($this->env, $this->source, $context["error"], "context", [], "any", false, false, false, 53)]), "html", null, true);
                echo "</td>
                </tr>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 56
            echo "            </tbody>
        </table>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 59
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/reports/errors.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  189 => 59,  181 => 56,  172 => 53,  168 => 52,  164 => 51,  161 => 50,  157 => 49,  142 => 39,  137 => 38,  133 => 37,  130 => 36,  126 => 34,  124 => 33,  121 => 32,  117 => 30,  111 => 29,  108 => 28,  102 => 25,  96 => 24,  93 => 23,  90 => 22,  85 => 21,  83 => 20,  79 => 18,  77 => 17,  68 => 11,  65 => 10,  61 => 9,  54 => 6,  50 => 5,  45 => 1,  43 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "/reports/errors.html.twig", "/reports/errors.html.twig");
    }
}

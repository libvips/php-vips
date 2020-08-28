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

/* /reports/markers.html.twig */
class __TwigTemplate_15ed159e75ab2e77dac2b8288fffdbf52fbb819c5603384c2be58116a2a674cc extends \Twig\Template
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
        $context["filesWithMarkers"] = twig_array_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "files", [], "any", false, false, false, 3), function ($__file__) use ($context, $macros) { $context["file"] = $__file__; return  !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["file"] ?? null), "markers", [], "any", false, false, false, 3)); });
        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "/reports/markers.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 6
        echo "    ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "name", [], "any", false, false, false, 6), "html", null, true);
        echo " &raquo; Markers
";
    }

    // line 9
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 10
        echo "    <ul class=\"phpdocumentor-breadcrumbs\">
        <li><a href=\"";
        // line 11
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["/index.html"]), "html", null, true);
        echo "\">Home</a></li>
    </ul>

    <div class=\"phpdocumentor-row\">
        <h2 class=\"phpdocumentor-content__title\">Markers</h2>

        ";
        // line 17
        if ( !twig_test_empty(($context["filesWithMarkers"] ?? null))) {
            // line 18
            echo "            <h3>Table of Contents</h3>
            <table class=\"phpdocumentor-table_of_contents\">
                ";
            // line 20
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["filesWithMarkers"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                // line 21
                echo "                    ";
                if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["file"], "markers", [], "any", false, false, false, 21), "count", [], "any", false, false, false, 21) > 0)) {
                    // line 22
                    echo "                        <tr>
                            <td class=\"phpdocumentor-cell\"><a href=\"#";
                    // line 23
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 23), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 23), "html", null, true);
                    echo "</a></td>
                            <td class=\"phpdocumentor-cell\">";
                    // line 24
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["file"], "markers", [], "any", false, false, false, 24), "count", [], "any", false, false, false, 24), "html", null, true);
                    echo "</td>
                        </tr>
                    ";
                }
                // line 27
                echo "                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 28
            echo "            </table>
        ";
        } else {
            // line 30
            echo "            <div class=\"phpdocumentor-admonition phpdocumentor-admonition--success\">
                No markers have been found in this project.
            </div>
        ";
        }
        // line 34
        echo "
        ";
        // line 35
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["filesWithMarkers"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
            // line 36
            echo "            <a id=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 36), "html", null, true);
            echo "\"></a>
            <h3><abbr title=\"";
            // line 37
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "path", [], "any", false, false, false, 37), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["file"], "name", [], "any", false, false, false, 37), "html", null, true);
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
            // line 47
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["file"], "markers", [], "any", false, false, false, 47));
            foreach ($context['_seq'] as $context["_key"] => $context["marker"]) {
                // line 48
                echo "                    <tr>
                        <td class=\"phpdocumentor-cell\">";
                // line 49
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["marker"], "type", [], "any", false, false, false, 49), "html", null, true);
                echo "</td>
                        <td class=\"phpdocumentor-cell\">";
                // line 50
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["marker"], "line", [], "any", false, false, false, 50), "html", null, true);
                echo "</td>
                        <td class=\"phpdocumentor-cell\">";
                // line 51
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["marker"], "message", [], "any", false, false, false, 51), "html", null, true);
                echo "</td>
                    </tr>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['marker'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 54
            echo "                </tbody>
            </table>
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
        return "/reports/markers.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  180 => 57,  172 => 54,  163 => 51,  159 => 50,  155 => 49,  152 => 48,  148 => 47,  133 => 37,  128 => 36,  124 => 35,  121 => 34,  115 => 30,  111 => 28,  105 => 27,  99 => 24,  93 => 23,  90 => 22,  87 => 21,  83 => 20,  79 => 18,  77 => 17,  68 => 11,  65 => 10,  61 => 9,  54 => 6,  50 => 5,  45 => 1,  43 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "/reports/markers.html.twig", "/reports/markers.html.twig");
    }
}

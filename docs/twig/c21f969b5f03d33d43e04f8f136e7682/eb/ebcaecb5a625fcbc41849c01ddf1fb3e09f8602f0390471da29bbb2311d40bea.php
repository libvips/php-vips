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

/* sidebar.html.twig */
class __TwigTemplate_d518fd1f8321f02dc3e80eecf7931a76fcb8af33b49de63da8fd6ec19c975bc8 extends \Twig\Template
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
        echo "<aside class=\"three phpdocumentor-columns phpdocumentor-sidebar\">
    <section data-search-form class=\"phpdocumentor-search\">
        <h1 class=\"phpdocumentor-sidebar__category-header\">Search</h1>
        <label class=\"phpdocumentor-label\">
            <span class=\"visually-hidden\">Search for</span>
            <input type=\"search\" class=\"phpdocumentor-field phpdocumentor-search__field\" placeholder=\"Loading ..\" disabled />
        </label>
    </section>

    ";
        // line 10
        if (($context["menu"] ?? null)) {
            // line 11
            echo "    ";
            echo twig_include($this->env, $context, "menu.html.twig", ["menuItem" => ($context["menu"] ?? null)], false);
            echo "
    ";
        }
        // line 13
        echo "
    <h1 class=\"phpdocumentor-sidebar__category-header\">Namespaces</h1>
    ";
        // line 15
        if ((($context["usesNamespaces"] ?? null) ||  !($context["usesPackages"] ?? null))) {
            // line 16
            echo "        <h2 class=\"phpdocumentor-sidebar__root-namespace\">";
            echo call_user_func_array($this->env->getFilter('route')->getCallable(), [twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 16), "Global"]);
            echo "</h2>
        ";
            // line 17
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "namespace", [], "any", false, false, false, 17), "children", [], "any", false, false, false, 17));
            foreach ($context['_seq'] as $context["_key"] => $context["namespace"]) {
                // line 18
                echo "            <h3 class=\"phpdocumentor-sidebar__root-namespace\">";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["namespace"], "class:short"]);
                echo "</h3>
            <ul class=\"phpdocumentor-list\">
                ";
                // line 20
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["namespace"], "children", [], "any", false, false, false, 20));
                foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                    // line 21
                    echo "                    <li>";
                    echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["child"], "class:short"]);
                    echo "</li>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 23
                echo "            </ul>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['namespace'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 25
            echo "    ";
        }
        // line 26
        echo "
    ";
        // line 27
        if (($context["usesPackages"] ?? null)) {
            // line 28
            echo "    <h1 class=\"phpdocumentor-sidebar__category-header\">Packages</h1>
    ";
            // line 29
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "package", [], "any", false, false, false, 29), "children", [], "any", false, false, false, 29));
            foreach ($context['_seq'] as $context["_key"] => $context["package"]) {
                // line 30
                echo "        <h2 class=\"phpdocumentor-sidebar__root-package\">";
                echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["package"], "class:short"]);
                echo "</h2>
        <ul class=\"phpdocumentor-list\">
            ";
                // line 32
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["package"], "children", [], "any", false, false, false, 32));
                foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                    // line 33
                    echo "                <li>";
                    echo call_user_func_array($this->env->getFilter('route')->getCallable(), [$context["child"], "class:short"]);
                    echo "</li>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 35
                echo "        </ul>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['package'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 37
            echo "    ";
        }
        // line 38
        echo "
    <h1 class=\"phpdocumentor-sidebar__category-header\">Reports</h1>
    ";
        // line 40
        if ((($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "settings", [], "any", false, false, false, 40), "custom", [], "any", false, false, false, 40)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["graphs.enabled"] ?? null) : null)) {
            // line 41
            echo "    <h2 class=\"phpdocumentor-sidebar__root-package\"><a href=\"";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["graphs/classes.html"]), "html", null, true);
            echo "\">Class Diagram</a></h2>
    ";
        }
        // line 43
        echo "    <h2 class=\"phpdocumentor-sidebar__root-package\"><a href=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["reports/deprecated.html"]), "html", null, true);
        echo "\">Deprecated</a></h2>
    <h2 class=\"phpdocumentor-sidebar__root-package\"><a href=\"";
        // line 44
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["reports/errors.html"]), "html", null, true);
        echo "\">Errors</a></h2>
    <h2 class=\"phpdocumentor-sidebar__root-package\"><a href=\"";
        // line 45
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["reports/markers.html"]), "html", null, true);
        echo "\">Markers</a></h2>
</aside>
";
    }

    public function getTemplateName()
    {
        return "sidebar.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  162 => 45,  158 => 44,  153 => 43,  147 => 41,  145 => 40,  141 => 38,  138 => 37,  131 => 35,  122 => 33,  118 => 32,  112 => 30,  108 => 29,  105 => 28,  103 => 27,  100 => 26,  97 => 25,  90 => 23,  81 => 21,  77 => 20,  71 => 18,  67 => 17,  62 => 16,  60 => 15,  56 => 13,  50 => 11,  48 => 10,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "sidebar.html.twig", "sidebar.html.twig");
    }
}

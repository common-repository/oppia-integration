=== Oppia integration plugin ===
Contributors: aleksandrsuhhinin
Tags: feeds, import, widget
Requires at least: 4.6
Requires PHP: 5.5
Tested up to: 4.8.2
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Fetches and embeds Courses, Events, Webinars, etc. from oppia.fi feed
== Description ==
This plugin fetches a data about Courses, Events, Webinars and another learning info from the oppia.fi site.
All fetched data can be placed in posts or in  widgets.
In admin panel user can set up  IDs and select type and number of items to show in an output of the plugin.
At the same page can be placed several outputs, (tested up to 10 per page, placed in posts and widgets).
To use this plugin, you need an [Oppia.fi API key] (you can get it by contact to info@oppia.fi).


== Installation ==

1. Download and activate the plugin.
2. Browse to the 'Oppia plugin' menu to configure.
3. Enter the API key in the 'Customer API-key' field and press 'Save changes'
4. After the form submitting a list of available themes will appears.
5. Enter a setting's name in the '...or create a new one:' field and select one or more themes, enter a number of items to fetch for each theme.
6. Press 'Save changes'
7. At the Display Options tab user can tune up a view of plugin's output.
    - Set the title
    - Set the title style by filling the 'Before title' and 'After title' fields (e.g. <h2 class="my_theme_class"> and </h2>)
    - Set the widget style by filling the 'Before widget' and 'After widget' fields (e.g. <div class="my_widget_class"> and </div>)
    - Set the widget height in pixels.

== Screenshots ==

1. Selection settings (Config name, API key and section selection) tab.
2. Display options tab. (tags to wrap title and widget).
3. Sample result output (with the 'Widget height' set to 300).

== Changelog ==
= 1.0 =
*Release date  - 4th October 2017



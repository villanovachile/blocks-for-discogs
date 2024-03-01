=== Plugin Name ===
Contributors: villanovachile
Tags: discogs, block, gutenberg block, wordpress block, vinyl, collection
Requires at least: 5.9
Tested up to: 6.4.3
Stable tag: 1.0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This is a WordPress Block that displays your music collection from Discogs.com

== Description ==

This is a WordPress plugin displays your music collection from Discogs.com in a WordPress Block. If you're using the Classic Editor, or a page builder such as Elementor, then you can still use its built in short code [blocks-for-discogs].

A live demo can be seen at https://danielr.io/projects/blocks-for-discogs-demo/

== Installation and Usage ==

1. Install and activate the plugin.
2. Navigate to Blocks for Discogs in the admin menu
3. Enter your Discogs.com username (required). If you don't already have a discogs.com account, you can register for a free account [here] (https://accounts.discogs.com/register).
4. Enter your Discogs.com token (required). You can view or generate a token [here](https://www.discogs.com/settings/developers).
5. If you're using the WordPress Block Editor, then you can search for the Blocks for Discogs block. Your collection will automatically be displayed, so as long as you have entered a valid username & token.
6. If you're using the Classic Editor, or a page builder such as Elementor, then you can use the shortcode [blocks-for-discogs].

This plugin employs infinite scroll to seamlessly load your collection as you continue to scroll. This is important to consider, as you will want to avoid placing content below this block/short code.

Currently only one block per page is supported, and only the default collection will be displayed. If you add multiple blocks to one page, only the first block will display content.

== Frequently Asked Questions ==

== Why isn't my collection displaying? ==

Ensure that both username and token are entered correctly. You must also ensure that your collection is publicly accessible by going [here](https://www.discogs.com/settings/privacy) and enabling "Allow others to browse my collection".

== Is it possible to ... ==

Currently with the initial release it is not possible to sort the collection, select a custom created collection, want lists, for sale lists, or customize how the collection displays. This functionality is intended to be added in future releases.

== Screenshots ==

1. How the block appears on the front end
2. How the block appears in the Editor
3. View of the settings page

== Changelog ==

### 1.0 - 2022-09-13

- Initial plugin release.

### 1.0.1 - 2023-03-05

-   Allow hyphens and periods in usernames.
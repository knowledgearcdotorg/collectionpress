# CollectionPress

Contributors: knowledgearc
Tags: dspace, rest, author page, authors
Requires at least: 4.5
Tested up to: 4.8
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

CollectionPress provides a variety of features for building author or researcher pages within Wordpress.

Items archived in DSpace can also be displayed through CollectionPress.

## Prerequisites

You need to be running a DSpace instance which is using the [KnowledgeArc REST API](https://github.com/knowledgearcdotorg/dspace).

The core REST API currently has limited functionality that is required for CollectionPress to deliver a full set of features. In particular, CollectionPress makes use of the DSpace Discovery (a simple wrapper for Apache Solr), which is currently only exposed via the KnowledgeArc REST API.

The CollectionPress roadmap includes supporting the DSpace REST API as it develops.

## Getting Started

1. Log into your Wordpress admin,
2. Install CollectionPress,
3. Configure CollectionPress by setting up a connection your DSpace instance (Settings -> CollectionPress),
4. Create a Page (Pages -> Add New). This page will be used for displaying your DSpace items,
5. Select the Page you have just created in step 4 using the **Item View Page** dropdown, available in the CollectionPress general settings (Settings -> CollectionPress),
6. Create a page or blog post. Add the collectionpress shortcode to display an author's items:

[collectionpress list="items" author="Author Name"]

You can also create Author Pages using the CollectionPress Author Pages manager (Authors -> Add).


## Settings

Available settings for configuring CollectionPress are:

### Rest Url
Rest Url is used to configure the DSpace REST endpoint. An example endpoint might look like:

http://myarchive.tld/rest

### Item View Page
An item page is used to display a DSpace item directly via the REST API. You will need to create a Wordpress Page and assign it using the Item View Page dropdown in order to view a DSpace item in Wordpress.

### Author List Page
The author list page displays a list of Author Pages. You will need to create a Wordpress Page and assign it using the Author List Page dropdown in order to browse authors.

## Author Pages

CollectionPress provides a feature called Author Pages.

Author Pages can be used for displaying information about an author or researcher, such as name, bio and location. You can also configure the Author Page to display the author's items.

To manage your Author Pages, click on "Authors" from the wp-admin main menu.

### Creating an Author Page

To create an author page, log into Wordpress admin and select Authors -> Add from the Dashboard menu or select Authors -> All and click on Add from the Authors page.

Once in the "Add New" page, specify your author's details.

When typing the author's name, a controlled list of names are retrieved from DSpace; in order for DSpace items to be listed in the Author Page, **the author name must exactly match an author name in DSpace**.

#### Author Info

You can use the Author Info section to specify what is shown on the author page as well as link the author page to a registered Wordpress author.

##### Show items for this Author

Tick to show the DSpace items corresponding to the Author's name. The author's name must exactly match an author in DSpace.

##### Show posts for this Author

Tick to show blog posts belonging to the author. You must link the author page to a registered Wordpress author for this feature to work.

##### Select Author

Select the registered Wordpress author to link to the Author Page. You only need to link a Wordpress author to an author page if **Show posts for this Author** is **Yes**.

### Link a Registered User to an Author Page

Author Pages can also be linked to existing Wordpress authors. Linking an Author Page to a registered author allows you to display the registered author's Wordpress blog posts within the Author Page.

You can link a registered author by editing the Author Page:

- Scroll to "Author Details",
- Check "Show Posts for this author",
- Select the registered Wordpress author from the drop down list,
- Save.

## Shortcode

The CollectionPress shortcode allows you to include DSpace item lists within blog posts, pages and other content which can parse shortcode.

**You do not need to use the CollectionPress shortcode in an Author Page**. Instead, tick **Show items for this Author** in the **Author Details** section of the Author Page editor.

The basic CollectionPress shortcode.

**Syntax:**

[collectionpress list="authors|items"]

**Params:**

*list*

The type of list to display. The default is "items".

## DSpace Items by Author

Lists the items associated with an author.

**Syntax:**

[collectionpress list="items" author="Author Name"]

**Params:**

*author*

The author parameter must exactly match the author name in DSpace.

## List Authors

Lists all authors who have an Author Page.

**Syntax:**

[collectionpress list="authors" limit="1"]

**Params:**

*limit*

The number of records to display per page.

## Template Overriding

CollectionPress output can be overridden in your Wordpress Theme.

All templates are located in frontend/template. To override, copy files from frontend/template to your theme's collectionpress directory then modify to meet your needs.

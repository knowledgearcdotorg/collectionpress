# CollectionPress
Wordpress Collections Archives and Repositories.

## Getting Started

1. Install CollectionPress,
2. Configure CollectionPress by setting up a connection to the dspace demo,
3. Create a Wordpress Page. This page will be used for displaying your DSpace items,
4. Select the Page you have just created using the Select Item Page dropdown, available in the CollectionPress general settings,
5. Create a page or blog post. Add the collectionpress shortcode to pull an author's items.


## Settings

Available settings for configuring CollectionPress are:  

### Rest Url
Rest Url is used to configure the DSpace REST endpoint. An example endpoint might look like:

http://myarchive.tld/rest


### Select Item Page
An item page is used to display a DSpace item directly via the REST API. You will need to create a Wordpress Page and assign it using the Select Item Page dropdown.

### Select Author Page
The author page displays a list of Author Pages. You will need to create a Wordpress Page and assign it using the Select Author Page dropdown.

## Author Pages

CollectionPress provides a feature called Author Pages.

Author Pages can be used for displaying information about an author or researcher, such as name, bio and location. You can also configure the Author Page to display the author's items.

To manage your Author Pages, click on "Authors" from the wp-admin main menu.

### Link a Registered User to an Author Page

Author Pages can also be linked to existing Wordpress authors. Linking an Author Page to a registered author allows you to display the registered author's Wordpress blog posts within the Author Page.

You can link a registered author by editing the Author Page:  

- Scroll to "Author Details",
- Check "Show Posts for this author",
- Select the registered Wordpress author from the drop down list,
- Save.


## Short Code

The CollectionPress shortcode allows you to include DSpace item lists within blog posts, pages and other content which can parse shortcode.

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
Limit can be used in both shortcodes. If it doesn't provided in shortcode it will take default wordpress value of posts_per_page.

## Template Overriding

CollectionPress output can be overridden in your Wordpress Theme.  
  
All templates are located in frontend/template. To override copy files from frontend/template to your theme's collectionpress directory then modify to meet your requirements.
The author single file can be pasted in theme root directory.

## Template Usage
Using template by 3 ways:
1). Select the pages for Items List and Author List.
2). If you pasted the template file as instructed above, then you have to choose template file in page options.
3). If didn't done anything from above points. Create 2 pages by name Items and Author List and there slug(url) should be items and author-list respectively.

# CollectionPress
Wordpress Collections Archives and Repositories.

## Getting Started

1. Install CollectionPress
2. Configure CollectionPress by setting up a connection to the dspace demo
3. Create a page or blog post. Add the collectionpress shortcode to pull an author's items.

## Settings

Available settings for configuring CollectionPress are:  

### Rest Url
Rest Url is used to configure the DSpace REST endpoint. An example endpoint might look like:

http://myarchive.tld/rest

### Item Url
The Item Url provides a general url for creating links to items. In DSpace this might look like:

http://myarchive.tld/handle

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

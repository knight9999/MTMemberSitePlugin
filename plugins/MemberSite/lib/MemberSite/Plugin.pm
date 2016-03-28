package MemberSite::Plugin;

use strict;

use MT;

# http://mt101.local/mt/mt.cgi?__mode=list&_type=member

sub list_member {
  my $json = <<'EOS';
{
   "error" : null,
   "result" : {
      "messages" : [],
      "count" : "158",
      "page" : "1",
      "page_max" : 4,
      "columns" : "id,title,author_name,category_id,authored_on,modified_on,comment_count,ping_count",
      "id" : "_allpass"
      "filters" : [
         {
            "can_save" : "1",
            "can_delete" : "1",
            "can_edit" : "1",
            "id" : "10",
            "label" : "Non Categorized Entries",
            "items" : [
               {
                  "args" : {
                     "value" : "0"
                  },
                  "type" : "category_id"
               }
            ]
         },
         // ...略
      ],
      "objects" : [
         [
            "179",
            "179",
            "My First Entry",
            "メロディ",
            "-",
            "17 hours ago",
            "17 hours ago",
            "<a href=\"http://example.com/mt.cgi?__mode=list&filter=entry&_type=comment&blog_id=2&filter_val=179\">0</a>",
            "<a href=\"http://example.com/mt.cgi?__mode=list&filter=entry_id&_type=ping&blog_id=2&filter_val=179\">0</a>"
         ],
         [
            "178",
            "178",
            "My Second Entry",
            "メロディ",
            "-",
            "17 hours ago",
            "17 hours ago",
            "<a href=\"http://example.com/mt.cgi?__mode=list&filter=entry&_type=comment&blog_id=2&filter_val=178\">0</a>",
            "<a href=\"http://example.com/mt.cgi?__mode=list&filter=entry_id&_type=ping&blog_id=2&filter_val=178\">0</a>"
         ],
         //...略
      ]
   }
}  
EOS

  $json;
}

1;


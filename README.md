# InstagramAPI
## Usage

### Get Profile Data
```php
profile("username");
```
### Get Post Data
```php
post("shortcode");
```
## Example
```php
<?php
include 'instagram.php';
echo profile("9gag");
echo post("BzRIu3_lsev");
?>
```

### Profile
+ Instagram ID
+ Profile Picture
+ Follower count
+ Following count
+ Biography
+ Total posts
+ Last 9 Posts
### Post
+ Likes Count
+ Comment Count
+ Caption
+ Media Link
+ Last 10 Comments

# Blogs

create .env place your database name

# Run commands
php artisan jwt:secret
php artisan optimize
php artisan migrate

1. After creating user you have to update is_admin to value 1 for admin. (for less time i created like this otherwise i create more efficiently).
2. create blog api you can update that blog also send key id with value
3. in search api send key title for search by title in body.

All the APIs are created as per requirements.

# Not completed ticket 
- filter by category 
- Also if UI is in the requirement.
(Unable to complete for less time)

Required key for APIs

1. Register - url - http://127.0.0.1:8000/api/register
              key - name, email, password, password_confirmation

2. Login -  url - http://127.0.0.1:8000/api/login
            key - email, password

3. Profile - url - http://127.0.0.1:8000/api/profile
             Authorization - Bearer token

4. Refresh token - url - http://127.0.0.1:8000/api/refresh
                   Authorization - Bearer token

5. Logout -      url - http://127.0.0.1:8000/api/logout
                 Authorization - Bearer token

6. Create Blog & update blog - url - http://127.0.0.1:8000/api/create
                 key - title, image, description, creator_name, creator_image, estimated_reading_time
                 if you add key valid_user = 1 then this blog will shown to logged in users
                 if you add key id = any blog id that blog will update
                 Authorization - Bearer token

7. All blogs - url - http://127.0.0.1:8000/api/blogs
               key - sortBy (id, created_at), sortOrder (asc, desc)
               if you add Authorization - Bearer token (all blogs will show)

8. single blog - url - http://127.0.0.1:8000/api/blogs/1
                       Authorization - Bearer token (without token also)

9. search by title - url - http://127.0.0.1:8000/api/search
                           Authorization - Bearer token (without token also)

10. Create category - url - http://127.0.0.1:8000/api/createcategory
                      key - category_name
                      Authorization - Bearer token

11. Add category to blog - url - http://127.0.0.1:8000/api/addcategorytoblog
                      key - category_id, blog_id
                      Authorization - Bearer token

12. Add favorites - url - http://127.0.0.1:8000/api/blogs/1/addfavorite
                    Authorization - Bearer token

13. Remove favorites - url - http://127.0.0.1:8000/api/blogs/1/removefavorite
                    Authorization - Bearer token

14. delete blog - url - http://127.0.0.1:8000/api/deleteblog/{id}
                    Authorization - Bearer token
# Postman API Requests - WikiEngine

## Base URL
```
https://yourdomain.com/api
```

## Authentication
All requests (except public ones) require JWT token in header:
```
Authorization: Bearer YOUR_JWT_TOKEN
```

---

## 1. ARTICLE CREATION (With All Features)

### Request
```
POST /api/articles
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "title": "Barack Obama",
  "slug": "barack-obama",
  "summary": "44th President of the United States",
  "content": "== Early Life ==\nBarack Obama was born in '''Hawaii''' in 1961.\n\n== Presidency ==\nHe served as President from [[2009]] to [[2017]].\n\n{{Infobox|name=Barack Obama|born=1961|office=President}}\n\n== See Also ==\n* [[Presidents of the United States]]\n* [[Democratic Party]]",
  "category_id": 1,
  "language_code": "en",
  "namespace": "Main",
  "is_minor": false,
  "is_bot": false,
  "parse_wikitext": true,
  "edit_summary": "Created new article about Barack Obama",
  "attributes": {
    "birth_date": "August 4, 1961",
    "birth_place": "Honolulu, Hawaii",
    "spouse": "Michelle Obama",
    "children": "Malia, Sasha"
  },
  "occupation": {
    "occupation": "Politician",
    "title": "President of the United States",
    "years_active": "2009-2017",
    "location": "Washington, D.C."
  },
  "politician": {
    "term": "2009-2017",
    "party": "Democratic",
    "predecessor": "George W. Bush",
    "successor": "Donald Trump"
  },
  "family": {
    "spouse": "Michelle Obama",
    "children": "Malia Obama, Sasha Obama"
  },
  "social_links": {
    "twitter": "https://twitter.com/barackobama",
    "facebook": "https://facebook.com/barackobama",
    "website": "https://barackobama.com"
  }
}
```

### Response (Success - 201)
```json
{
  "success": true,
  "message": "Article created successfully",
  "data": {
    "id": 1,
    "title": "Barack Obama",
    "slug": "barack-obama",
    "url": "https://yourdomain.com/barack-obama",
    "language_code": "en",
    "namespace": "Main",
    "is_minor": false,
    "is_bot": false,
    "content": "<h2>Early Life</h2><p>Barack Obama was born in <strong>Hawaii</strong> in 1961.</p><p><h2>Presidency</h2><p>He served as President from <a href=\"/2009\">2009</a> to <a href=\"/2017\">2017</a>.</p><div class=\"infobox\"><h3>Barack Obama</h3><p>1961</p></div>...",
    "category": {...},
    "creator": {...},
    "attributes": {...},
    "occupation": {...},
    "politician": {...},
    "family": {...},
    "links": [...],
    "created_at": "2026-01-14T23:00:00.000000Z"
  }
}
```

---

## 2. ARTICLE UPDATE (With Minor Edit)

### Request
```
PUT /api/articles/barack-obama
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "content": "== Early Life ==\nBarack Obama was born in '''Hawaii''' on August 4, 1961.\n\n== Presidency ==\nHe served as President from [[2009]] to [[2017]].",
  "is_minor": true,
  "edit_summary": "Fixed typo: added birth date",
  "parse_wikitext": true
}
```

### Response (Success - 200)
```json
{
  "success": true,
  "message": "Article updated successfully",
  "data": {
    "id": 1,
    "title": "Barack Obama",
    "is_minor": true,
    ...
  }
}
```

---

## 3. CREATE TEMPLATE

### Request
```
POST /api/templates
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "name": "Infobox",
  "description": "Information box template for articles",
  "content": "<div class=\"infobox\">\n  <h3>{{name}}</h3>\n  <p><strong>Born:</strong> {{born}}</p>\n  <p><strong>Office:</strong> {{office}}</p>\n  <p><strong>Term:</strong> {{term}}</p>\n</div>",
  "parameters": ["name", "born", "office", "term"],
  "is_active": true
}
```

### Response (Success - 201)
```json
{
  "success": true,
  "message": "Template created successfully",
  "data": {
    "id": 1,
    "name": "Infobox",
    "slug": "infobox",
    "description": "Information box template for articles",
    "parameters": ["name", "born", "office", "term"],
    "usage_count": 0
  }
}
```

---

## 4. LIST TEMPLATES

### Request
```
GET /api/templates?per_page=20&page=1&search=infobox
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
```

### Response (Success - 200)
```json
{
  "success": true,
  "message": "Templates retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Infobox",
      "slug": "infobox",
      "description": "Information box template for articles",
      "parameters": ["name", "born", "office", "term"],
      "usage_count": 5,
      "is_active": true,
      "creator": {
        "id": 1,
        "name": "John Doe"
      },
      "created_at": "2026-01-14T23:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

---

## 5. RENDER TEMPLATE

### Request
```
POST /api/templates/1/render
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "parameters": {
    "name": "Barack Obama",
    "born": "1961",
    "office": "President",
    "term": "2009-2017"
  }
}
```

### Response (Success - 200)
```json
{
  "success": true,
  "data": {
    "template_id": 1,
    "template_name": "Infobox",
    "rendered_content": "<div class=\"infobox\">\n  <h3>Barack Obama</h3>\n  <p><strong>Born:</strong> 1961</p>\n  <p><strong>Office:</strong> President</p>\n  <p><strong>Term:</strong> 2009-2017</p>\n</div>"
  }
}
```

---

## 6. GET ARTICLES BY NAMESPACE

### Request
```
GET /api/articles?namespace=User&language_code=en&page=1&limit=10
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
```

### Response (Success - 200)
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "User:JohnDoe",
      "slug": "user-johndoe",
      "namespace": "User",
      "language_code": "en",
      ...
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

---

## 7. ARTICLE WITH TEMPLATE (Wikitext Parsing)

### Request
```
POST /api/articles
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "title": "Test Article",
  "content": "== Introduction ==\nThis is a test article.\n\n{{Infobox|name=Test|born=1990|office=Developer|term=2020-2024}}\n\n== Content ==\n'''Bold text''' and ''italic text''.\n\n* Item 1\n* Item 2\n* Item 3\n\n[[Link to another page|Click here]]",
  "category_id": 1,
  "namespace": "Main",
  "parse_wikitext": true,
  "is_minor": false,
  "is_bot": false
}
```

### Response (Success - 201)
```json
{
  "success": true,
  "message": "Article created successfully",
  "data": {
    "id": 2,
    "title": "Test Article",
    "content": "<h2>Introduction</h2><p>This is a test article.</p><div class=\"infobox\"><h3>Test</h3><p><strong>Born:</strong> 1990</p><p><strong>Office:</strong> Developer</p><p><strong>Term:</strong> 2020-2024</p></div><h2>Content</h2><p><strong>Bold text</strong> and <em>italic text</em>.</p><ul><li>Item 1</li><li>Item 2</li><li>Item 3</li></ul><p><a href=\"/link-to-another-page\">Click here</a></p>",
    ...
  }
}
```

---

## 8. BOT EDIT EXAMPLE

### Request
```
PUT /api/articles/barack-obama
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "content": "Updated content via automated script",
  "is_bot": true,
  "is_minor": true,
  "edit_summary": "Automated category update"
}
```

---

## 9. GET ARTICLE WITH ALL FEATURES

### Request
```
GET /api/articles/barack-obama?lang=en
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
```

### Response (Success - 200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Barack Obama",
    "slug": "barack-obama",
    "url": "https://yourdomain.com/barack-obama",
    "language_code": "en",
    "namespace": "Main",
    "is_minor": false,
    "is_bot": false,
    "content": "...",
    "category": {...},
    "creator": {...},
    "available_languages": [
      {
        "code": "en",
        "name": "English",
        "native_name": "English",
        "is_current": true,
        "article_id": 1
      },
      {
        "code": "hi",
        "name": "Hindi",
        "native_name": "हिन्दी",
        "is_current": false,
        "article_id": 2
      }
    ],
    ...
  }
}
```

---

## 10. CREATE USER PAGE (Different Namespace)

### Request
```
POST /api/articles
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "title": "User:JohnDoe",
  "slug": "user-johndoe",
  "content": "== About Me ==\nI am a developer and wiki enthusiast.\n\n== My Contributions ==\n* [[Article 1]]\n* [[Article 2]]",
  "category_id": 1,
  "namespace": "User",
  "parse_wikitext": true
}
```

---

## 11. CREATE TEMPLATE (Complete Example)

### Request
```
POST /api/templates
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "name": "Infobox",
  "description": "Information box template for biographical articles",
  "content": "<div class=\"infobox\">\n  <h3>{{name}}</h3>\n  <table>\n    <tr><td><strong>Born:</strong></td><td>{{born}}</td></tr>\n    <tr><td><strong>Died:</strong></td><td>{{died}}</td></tr>\n    <tr><td><strong>Occupation:</strong></td><td>{{occupation}}</td></tr>\n    <tr><td><strong>Nationality:</strong></td><td>{{nationality}}</td></tr>\n    <tr><td><strong>Known for:</strong></td><td>{{known_for}}</td></tr>\n  </table>\n</div>",
  "parameters": ["name", "born", "died", "occupation", "nationality", "known_for"],
  "is_active": true
}
```

### Response (Success - 201)
```json
{
  "success": true,
  "message": "Template created successfully",
  "data": {
    "id": 1,
    "name": "Infobox",
    "slug": "infobox",
    "description": "Information box template for biographical articles",
    "parameters": ["name", "born", "died", "occupation", "nationality", "known_for"],
    "usage_count": 0
  }
}
```

---

## 12. UPDATE TEMPLATE

### Request
```
PUT /api/templates/1
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "description": "Updated information box template",
  "content": "<div class=\"infobox updated\">\n  <h3>{{name}}</h3>\n  <p><strong>Born:</strong> {{born}}</p>\n  <p><strong>Occupation:</strong> {{occupation}}</p>\n</div>",
  "parameters": ["name", "born", "occupation"],
  "is_active": true
}
```

### Response (Success - 200)
```json
{
  "success": true,
  "message": "Template updated successfully",
  "data": {
    "id": 1,
    "name": "Infobox",
    "slug": "infobox",
    "description": "Updated information box template",
    "parameters": ["name", "born", "occupation"]
  }
}
```

---

## 13. GET TEMPLATE DETAILS

### Request
```
GET /api/templates/1
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
```

### Response (Success - 200)
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Infobox",
    "slug": "infobox",
    "description": "Information box template for biographical articles",
    "content": "<div class=\"infobox\">\n  <h3>{{name}}</h3>\n  <table>...</table>\n</div>",
    "parameters": ["name", "born", "died", "occupation", "nationality", "known_for"],
    "usage_count": 5,
    "is_active": true,
    "creator": {
      "id": 1,
      "name": "John Doe"
    },
    "updater": {
      "id": 1,
      "name": "John Doe"
    },
    "created_at": "2026-01-14T23:00:00.000000Z",
    "updated_at": "2026-01-14T23:00:00.000000Z"
  }
}
```

---

## 14. LIST TEMPLATES (With Filters)

### Request
```
GET /api/templates?per_page=20&page=1&search=infobox&is_active=true
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
```

### Response (Success - 200)
```json
{
  "success": true,
  "message": "Templates retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Infobox",
      "slug": "infobox",
      "description": "Information box template",
      "parameters": ["name", "born", "occupation"],
      "usage_count": 10,
      "is_active": true,
      "creator": {
        "id": 1,
        "name": "John Doe"
      },
      "created_at": "2026-01-14T23:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Stub",
      "slug": "stub",
      "description": "Stub template for incomplete articles",
      "parameters": [],
      "usage_count": 25,
      "is_active": true,
      "creator": {
        "id": 1,
        "name": "John Doe"
      },
      "created_at": "2026-01-14T23:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 2
  }
}
```

---

## 15. RENDER TEMPLATE WITH PARAMETERS

### Request
```
POST /api/templates/1/render
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "parameters": {
    "name": "Barack Obama",
    "born": "August 4, 1961",
    "died": "",
    "occupation": "Politician",
    "nationality": "American",
    "known_for": "44th President of the United States"
  }
}
```

### Response (Success - 200)
```json
{
  "success": true,
  "data": {
    "template_id": 1,
    "template_name": "Infobox",
    "rendered_content": "<div class=\"infobox\">\n  <h3>Barack Obama</h3>\n  <table>\n    <tr><td><strong>Born:</strong></td><td>August 4, 1961</td></tr>\n    <tr><td><strong>Died:</strong></td><td></td></tr>\n    <tr><td><strong>Occupation:</strong></td><td>Politician</td></tr>\n    <tr><td><strong>Nationality:</strong></td><td>American</td></tr>\n    <tr><td><strong>Known for:</strong></td><td>44th President of the United States</td></tr>\n  </table>\n</div>"
  }
}
```

---

## 16. DELETE TEMPLATE (Admin Only)

### Request
```
DELETE /api/templates/1
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
```

### Response (Success - 200)
```json
{
  "success": true,
  "message": "Template deleted successfully"
}
```

### Response (Error - 400 if template in use)
```json
{
  "success": false,
  "message": "Cannot delete template. It is currently being used in 5 article(s)."
}
```

---

## 17. USE TEMPLATE IN ARTICLE (Wikitext)

### Request
```
POST /api/articles
```

### Headers
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

### Body (JSON)
```json
{
  "title": "Barack Obama",
  "content": "== Introduction ==\nBarack Obama is the 44th President.\n\n{{Infobox|name=Barack Obama|born=August 4, 1961|occupation=Politician|nationality=American|known_for=44th President}}\n\n== Early Life ==\nHe was born in Hawaii.\n\n== See Also ==\n* [[Presidents of the United States]]\n* [[Democratic Party]]",
  "category_id": 1,
  "namespace": "Main",
  "parse_wikitext": true
}
```

### Response
Template automatically rendered in content when `parse_wikitext: true`

---

## Common Template Examples

### 1. Infobox Template
```json
{
  "name": "Infobox",
  "content": "<div class='infobox'><h3>{{name}}</h3><p>{{description}}</p></div>",
  "parameters": ["name", "description"]
}
```

### 2. Stub Template
```json
{
  "name": "Stub",
  "content": "<div class='stub'>This article is a stub. You can help by expanding it.</div>",
  "parameters": []
}
```

### 3. Citation Template
```json
{
  "name": "Citation",
  "content": "<cite>{{author}}, ''{{title}}'', {{publisher}}, {{year}}</cite>",
  "parameters": ["author", "title", "publisher", "year"]
}
```

### 4. Navigation Template
```json
{
  "name": "Navbox",
  "content": "<div class='navbox'><h4>{{title}}</h4><ul>{{items}}</ul></div>",
  "parameters": ["title", "items"]
}
```

---

## Common Namespaces

- `Main` - Regular articles (default)
- `User` - User pages
- `User_Talk` - User talk pages
- `Template` - Template pages
- `Category` - Category pages
- `File` - File pages
- `Help` - Help pages
- `Project` - Project pages

---

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated. Please log in."
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "title": ["The title field is required."],
    "category_id": ["The category id field is required."]
  }
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Article not found."
}
```

---

## Tips for Postman

1. **Environment Variables:**
   - `base_url`: `https://yourdomain.com/api`
   - `token`: `YOUR_JWT_TOKEN`

2. **Pre-request Script (for token):**
   ```javascript
   pm.environment.set("token", "YOUR_JWT_TOKEN");
   ```

3. **Use Variables:**
   ```
   {{base_url}}/articles
   Authorization: Bearer {{token}}
   ```

4. **Collection Organization:**
   - Articles
   - Templates
   - Languages
   - Files
   - Users

---

---

## TEMPLATE REQUESTS (Complete Guide)

### 1. CREATE TEMPLATE - Infobox

**Method:** `POST`  
**URL:** `https://yourdomain.com/api/templates`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

**Body:**
```json
{
  "name": "Infobox",
  "description": "Information box template for biographical articles",
  "content": "<div class=\"infobox\">\n  <h3>{{name}}</h3>\n  <table>\n    <tr><td><strong>Born:</strong></td><td>{{born}}</td></tr>\n    <tr><td><strong>Died:</strong></td><td>{{died}}</td></tr>\n    <tr><td><strong>Occupation:</strong></td><td>{{occupation}}</td></tr>\n    <tr><td><strong>Nationality:</strong></td><td>{{nationality}}</td></tr>\n    <tr><td><strong>Known for:</strong></td><td>{{known_for}}</td></tr>\n  </table>\n</div>",
  "parameters": ["name", "born", "died", "occupation", "nationality", "known_for"],
  "is_active": true
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Template created successfully",
  "data": {
    "id": 1,
    "name": "Infobox",
    "slug": "infobox",
    "description": "Information box template for biographical articles",
    "parameters": ["name", "born", "died", "occupation", "nationality", "known_for"]
  }
}
```

---

### 2. CREATE TEMPLATE - Stub

**Method:** `POST`  
**URL:** `https://yourdomain.com/api/templates`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

**Body:**
```json
{
  "name": "Stub",
  "description": "Stub template for incomplete articles",
  "content": "<div class=\"stub\">\n  <p>This article is a <strong>stub</strong>. You can help by <a href=\"/Special:ExpandArticle\">expanding it</a>.</p>\n</div>",
  "parameters": [],
  "is_active": true
}
```

---

### 3. CREATE TEMPLATE - Citation

**Method:** `POST`  
**URL:** `https://yourdomain.com/api/templates`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

**Body:**
```json
{
  "name": "Citation",
  "description": "Citation template for references",
  "content": "<cite>{{author}}. ''{{title}}''. {{publisher}}, {{year}}. {{isbn}}</cite>",
  "parameters": ["author", "title", "publisher", "year", "isbn"],
  "is_active": true
}
```

---

### 4. GET ALL TEMPLATES

**Method:** `GET`  
**URL:** `https://yourdomain.com/api/templates?per_page=20&page=1&search=infobox&is_active=true`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Response (200):**
```json
{
  "success": true,
  "message": "Templates retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Infobox",
      "slug": "infobox",
      "description": "Information box template",
      "parameters": ["name", "born", "occupation"],
      "usage_count": 10,
      "is_active": true,
      "creator": {
        "id": 1,
        "name": "John Doe"
      },
      "created_at": "2026-01-14T23:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 1
  }
}
```

---

### 5. GET TEMPLATE BY ID

**Method:** `GET`  
**URL:** `https://yourdomain.com/api/templates/1`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Infobox",
    "slug": "infobox",
    "description": "Information box template",
    "content": "<div class=\"infobox\">\n  <h3>{{name}}</h3>\n  <p>Born: {{born}}</p>\n</div>",
    "parameters": ["name", "born", "occupation"],
    "usage_count": 5,
    "is_active": true,
    "creator": {
      "id": 1,
      "name": "John Doe"
    },
    "updater": null,
    "created_at": "2026-01-14T23:00:00.000000Z",
    "updated_at": "2026-01-14T23:00:00.000000Z"
  }
}
```

---

### 6. UPDATE TEMPLATE

**Method:** `PUT`  
**URL:** `https://yourdomain.com/api/templates/1`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

**Body:**
```json
{
  "description": "Updated information box template with new styling",
  "content": "<div class=\"infobox updated\">\n  <h3>{{name}}</h3>\n  <div class=\"info-grid\">\n    <div><strong>Born:</strong> {{born}}</div>\n    <div><strong>Occupation:</strong> {{occupation}}</div>\n  </div>\n</div>",
  "parameters": ["name", "born", "occupation"],
  "is_active": true
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Template updated successfully",
  "data": {
    "id": 1,
    "name": "Infobox",
    "slug": "infobox",
    "description": "Updated information box template with new styling",
    "parameters": ["name", "born", "occupation"]
  }
}
```

---

### 7. RENDER TEMPLATE

**Method:** `POST`  
**URL:** `https://yourdomain.com/api/templates/1/render`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

**Body:**
```json
{
  "parameters": {
    "name": "Barack Obama",
    "born": "August 4, 1961",
    "died": "",
    "occupation": "Politician",
    "nationality": "American",
    "known_for": "44th President of the United States"
  }
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "template_id": 1,
    "template_name": "Infobox",
    "rendered_content": "<div class=\"infobox\">\n  <h3>Barack Obama</h3>\n  <table>\n    <tr><td><strong>Born:</strong></td><td>August 4, 1961</td></tr>\n    <tr><td><strong>Died:</strong></td><td></td></tr>\n    <tr><td><strong>Occupation:</strong></td><td>Politician</td></tr>\n    <tr><td><strong>Nationality:</strong></td><td>American</td></tr>\n    <tr><td><strong>Known for:</strong></td><td>44th President of the United States</td></tr>\n  </table>\n</div>"
  }
}
```

---

### 8. DELETE TEMPLATE (Admin Only)

**Method:** `DELETE`  
**URL:** `https://yourdomain.com/api/templates/1`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Response (200):**
```json
{
  "success": true,
  "message": "Template deleted successfully"
}
```

**Response (400 - If template in use):**
```json
{
  "success": false,
  "message": "Cannot delete template. It is currently being used in 5 article(s)."
}
```

---

### 9. USE TEMPLATE IN ARTICLE

**Method:** `POST`  
**URL:** `https://yourdomain.com/api/articles`  
**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
Content-Type: application/json
```

**Body:**
```json
{
  "title": "Barack Obama",
  "content": "== Introduction ==\nBarack Obama is the 44th President of the United States.\n\n{{Infobox|name=Barack Obama|born=August 4, 1961|occupation=Politician|nationality=American|known_for=44th President}}\n\n== Early Life ==\nHe was born in Hawaii.\n\n== See Also ==\n* [[Presidents of the United States]]\n* [[Democratic Party]]",
  "category_id": 1,
  "namespace": "Main",
  "parse_wikitext": true
}
```

**Note:** Template automatically rendered when `parse_wikitext: true`

---

## Template Parameter Examples

### Simple Template (No Parameters)
```json
{
  "name": "Stub",
  "content": "<div class='stub'>This article is a stub.</div>",
  "parameters": []
}
```

### Template with Required Parameters
```json
{
  "name": "Infobox",
  "content": "<div><h3>{{name}}</h3><p>{{description}}</p></div>",
  "parameters": ["name", "description"]
}
```

### Template with Optional Parameters
```json
{
  "name": "Citation",
  "content": "<cite>{{author}}. ''{{title}}''. {{publisher}}{{year}}</cite>",
  "parameters": ["author", "title", "publisher", "year"]
}
```

---

## Complete Postman Collection JSON

Yeh file ko Postman mein import kar sakte ho:

```json
{
  "info": {
    "name": "WikiEngine API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Create Article",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Authorization",
            "value": "Bearer {{token}}",
            "type": "text"
          },
          {
            "key": "Content-Type",
            "value": "application/json",
            "type": "text"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"title\": \"Test Article\",\n  \"content\": \"==Heading==\\nContent here\",\n  \"category_id\": 1,\n  \"namespace\": \"Main\",\n  \"parse_wikitext\": true\n}"
        },
        "url": {
          "raw": "{{base_url}}/articles",
          "host": ["{{base_url}}"],
          "path": ["articles"]
        }
      }
    }
  ]
}
```

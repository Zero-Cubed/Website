#Zero Cubed
##Web Authentication System


Users will find themselves having to authenticate themselves both on the variety of Zero Cubed website as well as the game launcher. This means that there needs to be a universal, easily used authentication API for all Zero Cubed products requiring authentication. This document details that API.

##Basic Login Mechanism


1. The user enters their credentials
2. The authentication system verifies their credentials, and returns a token
3. This token is stored and used in all future requests for account information

All of these requests are made using POST, and all data returned is in JSON.

###Request Statuses


For each request that you make, a status code will be returned. This status tells you about the success of the performed request. These codes are based on HTTP status codes.

* 200 - OK, no problems with request
* 400 - Invalid request, method specified may be nonexistent or needed parameters not specified
* 401 - Token not specified or invalid
* 404 - Not found, user ID or supplied credentials are incorrect
* 500 - Internal server error
* 503 - Authentication system down for maintenance

**ALWAYS CHECK THIS STATUS CODE BEFORE DOING ANYTHING ELSE!**


##Request Methods


All requests, except for login, require the user's token to be specified as a parameter. In addition, all requests, including login, require the app's secret key to be specified as a parameter.

Some methods require certain parameters, wereas in others they are optional.

###login

**Parameters:** username, password

**Return if successful:** `{"token":"aabbccddeeff1234", "status":200}`
    
                                
###logout

**Parameters:** None

**Return if successful:** `{"status":200}`
   
                                
###gamelist

**Parameters:** userid (optional, if not specified data for user signed in will be returned)

**Description:** Returns a list of the IDs of the games that the user owns

**Return if successful:** `{"games": [1,2,3,4], "status":200}`

###friendlist

**Parameters:** userid (optional, if not specified data for user signed in will be returned)

**Description:** Returns a list of the IDs of the users that a user is friends with

**Return if successful:** `{"friends": [567,223,97], "status":200}`
    
###outfriendlist

**Parameters:** None

**Description:** Returns a list of the IDs of the users that the user has friendrequested.

**Return if successful:** `{"outfriends": [34,788,929], "status":200}`
    
###infriendlist

**Parameters:** None

**Description:** Returns a list of the IDs of the users that have friendrequested the user.

**Return if successful:** `{"infriends":[12,85,976], "status":200}`
    
###profile

**Parameters:** userid (optional, if not specified data for user signed in will be returned)

**Description:** Returns all profile information of a user.

**Return if successful:**
<br/>
`{"name":"janskyd","email":"test@example.com","bio":"this is a bio", "status":200}` - for the user signed in
<br/>
`{"name":"janskyd","bio":"this is a bio", "status":200}` - for a different user

##Example Request

### Logging in

1. Request is made to API url with POST parameters `appsecret=1u537j831KLasd,method=login,username=foo,password=bar`
2. JSON data returned: `{"status":404}` - the username or password was incorrect

### Getting User ID 97's Friend list

1. Request is made to API url with POST parameters `appsecret=1u537j831KLasd,token=aks432ACn1kln8tYhg5422,method=friendlist,userid=97`
2. JSON data returned: `{"friends": [567,223,45], "status":200}`

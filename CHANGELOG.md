<!--- BEGIN HEADER -->
# Changelog

All notable changes to this project will be documented in this file.
<!--- END HEADER -->

## [1.0.0-dev](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/compare/2.11.6...1.0.0-dev) (2023-07-24)

### Features

* Adaptar FeesCalculator para utilizar RedisAdapter (#9) ([985dd8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/985dd888b58694b0073afb8f19d69fcad40342c6))
* Add Attribute Route to declare Routes/Endpoints ([2e35cb](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2e35cb9949a7b84e8ac443f066723f91e7621e2a))
* Add Basic authentication to Elastic Search ([903dfe](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/903dfe3b2c548b8e09a49407b821235ab8a72096))
* Add changes to several classes ([75e202](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/75e20202edb988a2bb716099a27b6a52483324cd))
* Add Criteria to Query Builder ([999c2f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/999c2f9a8c76523487328e411fedea8f1bdfb57f))
* Add default limit to queries with QueryBuilder if not present ([a7168a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a7168a5f20c4a7432dc1ec999d5e44c9aa8b9e5b))
* Add ElasticSearch Adapter / Client ([cc0c53](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/cc0c539d5652795195e5aa32bd4b1084b66249ee))
* Add ElasticSearch Dependency ([39597d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/39597d1c4cbdc383461ac882c0a867c866c9d762))
* Add Immutable unaccent to filter criteria ([d66158](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d6615844cd1d4dd54256c1ddb78bd048ef068b11))
* Add kosovo codes (#8) ([5c0e02](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5c0e02e52abfb4c6d20e9c7734466554294912bb))
* Add lower and unaccent to criteria filter, converting all fields to text. ([c73535](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c73535d364b80bcb02362bf6331cf0d1bfd83fc7))
* Add method to retrieve query params in Request ([041cf6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/041cf61a7466f903dd007d603ac762ea336fc7b2))
* Add MultiLingualText Class ([d0eb92](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d0eb925f32aaf4ef45abcea920503ff8e2f2e661))
* Add PostFilterDto to normalise filter querystring parameters. Add hash method to CriteriaDto and add Map dependency ([9a6186](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9a6186a0933fb82c22dcf63e4cf464939fa37c9f))
* Add service providers (#13) ([10a1b6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/10a1b62485b2bc04038b826b99d9a66322e92c5f))
* Add setMultiLang method in Doctrine QueryBuilder ([aae61c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/aae61ce62a18504ebf50111fac4c793a09a4dc42))
* Add toArrayWithLangs to return all languages in address related objects ([aec31b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/aec31bb59572cdb6c275041339299c606d19392f))
* Change Method Names, remove get string from getters ([e95b3e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e95b3eb76ea1d6a2cb639600e9055be0c4621f69))
* Change QueryBuilder to Doctrine, and adapt Criteria Query to regular and jsonb columns ([d7f9ff](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d7f9ff0eedf86c9e0714bbe218d267aebde37caf))
* Dump server (#16) ([070782](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/070782174ddd097822b6cd9314edca5085b45674))
* Include method in querybuilder to retrieve fallback language if requested language do not exist. ([da1e7f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/da1e7fe725d4ec96a584cd8a998de8217e057476))
* Make private client in ElasticSearchAdapter in order to be inaccesible ([836353](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/836353e7c7e6a771000fdd71b781d810e5f80cc5))
* Write logs and error functions to stdout in non-local envs ([39d0d8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/39d0d8823a474413e7f8925017e712517f988e00))

##### Authentication

* Revisit Authentication and Authorization functionalities ([591e09](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/591e0968f4c64f1ebca7ba5787ba56ff0343e334))

##### Autocomplete

* Add Autocomplete capabilities. Author: emalmierca ([9a53b2](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9a53b239c9cdbff0808454a63fb5b188cf620f22))

##### Check Health

* Add parallel health checks and timeout ([11a6c1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/11a6c17d6cb742dbe2cd5f31ce5e189e39c16294))

##### Command Bus

* Improve Async Coroutine ([d451ed](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d451edfc39edd269aeb47ddfdd5e2c359af99043))

##### Container

* Add when method to Container ([731ce1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/731ce1cb1b46690c13ff16afd58c649d1e6c6b08))

##### Coordinates

* Add Coordinates VO and tests ([93480c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/93480c2303db2e3e673ec0d832deefa28a09d93d))

##### Country/location

* Return '' empty string instead of null in names ([38f6f7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/38f6f79f904cd078509da3f7b7b6c70c85c07fa4))

##### Criteria

* Allow use of array of search params in Criteria ([b19a34](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/b19a349f37304e48a3c6f2a481068f481fa39cdc))
* Distinguish between strings/non-strings values to apply different where clauses ([9f8988](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9f898840201b2ef3df6c99795796ebe2773ecd2b))
* Unify normal and jsonb type columns under the same method: addQueryCriteria() ([e9c0ea](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e9c0eaabe81203f32857523b0e83436ca95df22d))

##### Criteria-and-filter

* Creación de clases QueryCriteria y FilterCriteria ([faf643](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/faf643108243388f5decc46466da4e021e0b5465))

##### Crontab

* Add scheduled class execution using attribute Crontab ([6eb6e0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6eb6e080f77b20d4a709e2edf16b49e17f65c0d1))

##### Date Time

* Add DateTime ValueObject ([5ef623](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5ef6238ec21b7317952817bfbed47ff5f24aa2e2))

##### Doctrine Query Builder

* Add methods iterateAssociative, iterateColumn, iterateKeyValue to received results as Generators ([7987d9](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7987d9d25ce5a2744a079cf42262f0ee273a7182))

##### Error Handling

* Improve Handle error in app for sync and async ([671d43](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/671d43b4d6aeeef95787e38315903d7f5f67a5d8))

##### Exceptions

* Add CustomException to be extended ([0a1a61](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0a1a616286e74fc7be2cfd9b58c1a312d061089d))

##### Filter Criteria

* Add post filtering class ([e56977](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e5697744730f14468c58946f86c7befad4c6aace))
* Modify response with min and max attributes for range filters ([fa1004](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fa1004cb3ee46c9f9ddcdf9df023ad38e8307934))

##### Health

* Add CheckHealthNoDB ([f51098](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f51098fc53ef567c505d78dc133a05980bf3cac2))
* Check read and write DB, Elastic and Redis connections ([76ced2](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/76ced2513660b199fdfdb33452a5b5876da6c1b1))

##### Httpclient

* Add streamWrapper method to HttpClient ([8265bd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8265bdf93bbe3bbab36a784a195127ce604078b7))

##### Language

* Add Language VO and emthod get lang in kernel ([b002a3](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/b002a3fe63c22b6904ae3e20caaf36cdd165f494))
* Add local as accepted_language, only because it is something interesting :P ([06ce9f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/06ce9fcffb358e9077fded63953046b9933846e3))

##### Library

* Add nikic/iter library to work with iteration primitives using generators ([0abe87](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0abe8762b010cd11b9426ba5254f356494af93a3))

##### Locations Service

* Add searchSimilarLocation functionality ([7ca26a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7ca26ae9cd1d8fec6b2c90b6983dac525d5592a0))

##### Master- Common- Services

* Add Services to retrieve Countriies from ElasticSearch ([55575e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/55575e790dd194babc10736b4a24bf0ee47dfeae))

##### Masters

* Add Get Airport By IATA Service ([898d95](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/898d95357a50345cc19ca0e431a31dbd26597555))
* Add Support Service to retrieve information about Location in master domain ([7ead98](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7ead9887e8aa8fb6f610faafc150ccc2a057d4d8))

##### Messaging

* Control errors processing received messages from broker ([0b3200](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0b32005a99d303f60ac94123eb60a0d48816f5a4))

##### Migrations

* Add Doctrine Migrations dependency ([867fae](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/867fae5166c4c3b10851d2227b085eb7c15bc2a7))

##### Pagination

* Improve exception message for clients ([0c300d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0c300db5cc874b3a2f663cf670bfce954c1a44aa))

##### Person Name

* Add toArray method ([9cbc13](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9cbc13ad765bf6ae6f051c1ce1bd291ec6afa8a6))

##### Phone

* Add Phone and PhonePrefix ValueObjects ([46f28c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/46f28c580f68cce9ee8374cb225795d40477413f))

##### Query Builder

* Add executeQuery method ([5a62fd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5a62fd5360be390d53e7a56e73299400e192c3ef))

##### Query Criteria

* Added support to use QueryCriteria with DoctrineQueryBuilder ([85afab](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/85afab54acaf5c89db6a3e51106f41483f19e3f9))
* Add method to be able to include any param/value from any source such as a dynamic URI ([873210](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8732107b9a53a65a7f2f3042835ad2b5b3206108))
* Create QueryCriteria objecto in order to parse all incoming parameters and filters from client http ([3c4ba7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3c4ba79b1306d64b0ae1ae1b0e450a7a909f1c6e))

##### Querybuilder

* Add individual parameter to querybuilder ([24b035](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/24b035273d44129306b590a08f386a60cc1066eb))
* Add type params to bindValues ([8ccd49](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8ccd49e9758feeb10162d5810cf63e24a57d6884))
* Allow including types in params in setParamaters ([a4b6e1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a4b6e1e74e4941253d0d9c352345d4a8d4a3f84e))

##### Redis

* Add exists, hExists, expire y raw methods in Redis Client ([440a57](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/440a57102adedd856c7dc9540c8c9929fa6fd666))
* Add new RedisAdapter class that will replace RedisClient ([203fe1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/203fe17d208b271dc587e6aff63962158228bfbc))
* Improve transaction methods ([836096](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/836096686c0e9f4102b790c7a227b7ca823c1a8d))

##### Redis Adapter

* Add method ttl and add options to set to allow EX/NX params as array ([ea16c0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/ea16c0690acd577496d9c453422ba3b37e210d0c))

##### Redis Table

* Add insertMultiple in order to add an array of items ([2491d0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2491d064d2b6a8565a455847eff7fbd6b4b81eea))
* Add RedisTable Class to ease working and filtering datasets in a more "sql table" way ([bd89d4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/bd89d498a638b11690334a0437493a6eec5ab173))
* Improve class ([9544ac](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9544acb32136d5b2da8061ac23d0b2e8c1ad0dbb))

##### Redis-conneciton

* Add password if exists ([f7837d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f7837d79684b788436d7f7c42881351a4fd74fb9))

##### Request

* Add TenantId getter from request ([c69d7c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c69d7cf26ce4a38f5e3139f9c6f61cf3654d21ee))

##### Response

* Add headers with commit and tag version to responses ([6a325d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6a325d66c84ffe38c59e00ad354034115f46071a))
* Add methods make Repone configurable and tests ([e068d1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e068d1421c222f8fdf2ab69677bc444748910e10))
* Response:json now converts all attributes to snake_case ([fa91bf](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fa91bfa391d893a08d44bc6bcdbe520b0de721f3))

##### Sentry

* Add Sentry to kernel ([d001f8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d001f850a01b5c569a2e4f15146d9b149d1ac570))

##### Subscribing

* Classes can now subscribe to events with PHP Attributes ([0e576c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0e576c3e94d5612108e506275d37e9eb729b47fb))

##### URL

* Create Url base VO ([e8bd59](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e8bd5986b57c9356926d013dd7a1382e040a4e51))

##### UUID

* Upgrade to UUID V7 ([8af8f6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8af8f6d66eb638f8a39d969d0a278be6584de113))

##### Value Objects

* Add CancellationPolicy, IATA, and add methods to DateTime ([4babfa](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4babfa1ad167f07c33b10a3431aa8eb29b784c51))

### Bug Fixes

* Add dev errors to dev output only in debug mode ([1f7c76](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1f7c769e273d96ee209884edecd5af79c77db78d))
* Add equals method to some VOs wrongly removed ([5982e4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5982e4690027c0960d9197181aaaa6fed2ad14ae))
* Add Global Container Dependency injection in HandleHttp ([fbb3ab](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fbb3ab7e5687fcd867ba5f50170531415cb0e168))
* Add null to return type option ([2cd426](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2cd42672430b028cbec4ed4979a3b38dcfdc0a01))
* Add use of couroutines in JobScheduler process ([6eb8fc](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6eb8fc7a724ada2444f12f91cb4b4595b4f0a6aa))
* Add variadic ([f6986e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f6986e100f1f5b145ea93deb969cb11433d5b991))
* Attribute parsing redeclares classes triggering php errors ([7fe298](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7fe2989adafd58a925dca74c872190ddf0c722c9))
* Avoid invoking connection before established ([4ea548](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4ea54836ba04b7455ce30c06e9ab630606578cda))
* Change Interface isEventSourced reconstituteFrom return type ([2d38df](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2d38dfca96ab7972a6aba3301651097762daf70c))
* Change param name to be coherent ([9db16a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9db16af5d1d9dcdc1299c27665b7cce053b2a516))
* Check if accept-language is not present ([36ff16](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/36ff16c715a2e636f92e9be6fdef35c8049dd16a))
* CriteriaExtended case insensitive search ([bd3c34](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/bd3c34c03c061f16c5840e377629bdbcf519d117))
* CriteriaExtended have a unique where ([c7a45b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c7a45bfd55a39552875e2b31ddf917dff1e0efbd))
* Error parsing and replacing binding variables like name and name2 ([5b3b46](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5b3b46ba82456a65303d499069556a330f4f3720))
* Fix adding routes both by attributes and provider ([781ca7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/781ca710c67c6e2fdf400b1ab9df83371a86e381))
* Fix addressDTO if coordinates are null ([591d8f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/591d8f2f9cfe14aa3f7e868727f49649f15ac122))
* Fix base_path function and global variable ([fee5a7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fee5a7774360a0303d0bbfb46e24358bbdff6639))
* Fix illegal operation in params in Logger ([486962](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4869628ea02c1f44430c84293143857669c7a39f))
* Fix namespace for PersistenceException ([02a59a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/02a59a33c4431a605d9371c6491e27b492b0630d))
* Fix null object pattern in LocationEntity and CountryEntity ([114e11](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/114e11b7b30342abe26284ac72d1bd484ece09e0))
* Fix number of sprintf params ([eb89ba](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/eb89ba9aa0407b96ce89ff40bd5da60c86840511))
* Fix return type ([ed77cf](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/ed77cfae6282b0a2a379d0b705c63fc45f355751))
* Fix routes in logger ([3f0f6a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3f0f6acb212c6924dcd3cc291d1ebf33e46edc1a))
* Fix two bugs in SqlQueryBuilder and PostgreSQLEventStoreAdapter ([01367e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/01367e1dda35c372245ef775276453be15822b69))
* Fix type ([352547](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3525475dd621b1119406785e1bcda19e6461bac4))
* Fix typo in query ([a53948](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a53948764c7d978d09ff1ff59308389e7fe964de))
* Fix typos in routes generated by automatic refactor ([76e066](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/76e0665b1c1c538bd114758333dd1b596b5ec04c))
* Fix value visibility ([0a41fb](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0a41fb9304abbdf5f75276113820ea13da2fb23d))
* Fix wrong namespaces ([77578f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/77578f8c4e28ab5dc6113f158619a677166d11c3))
* Improve and fix Authorizations ([d37a19](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d37a191b2f9382e964d523c56dbc3b115112cc58))
* Improve index creation in ES ([c597d4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c597d4766f1ce24b9210805236c3f622b28ac67f))
* Invoke AuthorizationsMiddleware with getInstance as it is a singleton ([cadc41](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/cadc411a4079ce3594da1bd70133ae3ef7e7b696))
* Map Static values in AuthorizationsProvider Enum from strings ([5f1fb5](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5f1fb5a09ad813f038b1185e7e5783e8d6890f60))
* Modify fromScalars params to make them actual scalars ([811707](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8117070d7b329b9fa1adae8e91f397139fc8789c))
* MultiLingualText Namespace ([c19001](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c190013f67d430089617716a087c977810970e6d))
* OrderBy ([317f95](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/317f9547ed5fd3e771a54dfce416e195e8f47d47))
* QueryBuilder package folder route ([4ccf85](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4ccf85c423283b90e15de4cd6466cbb849549a2f))
* Recurrent autoinvoke in transactionId getter ([62e790](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/62e790e86ff6504bd715a5f4521eb79cfda3de61))
* Remove bug line ([720ddc](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/720ddc7bdd100edfa59b5c4072ab7d7425923ae1))
* Remove permission ([62bea0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/62bea0e6f4578dcf750dabc3b3f6521807a7c315))
* Removing from in paging query when there are no results caused an error in some edge cases ([00113b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/00113bcb5de44f543db91cea122b98e20f305a21))
* Request acceptLanguage may break with no header ([d629b1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d629b10c7a3aa28fc9abe4e802c8f805f724edff))
* Response error in syncHandle ([9926ea](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9926ea266136816a30f200bb8971e6305c8b48a0))
* Return empty array if Redis returns false in json gets ([c7e4c4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c7e4c49183447d2e23451efd8711552ff1e47829))
* Return null if key is not found by redis in json get methods ([56eca3](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/56eca3306bf56c53a07eab695c8b64b128b2f03c))

##### Address Complete Dto

* Fix error with country names ([88ae5b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/88ae5b35bf99a2bf6128cc7ce9cbabfd76a2f5d6))

##### Airport Dto

* Fix method invokation du to changes in kernel ([6be962](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6be962bb3d197099aa428d5995fe8ca3270e55c7))

##### Authinfo

* Fix differents types into one to be passed to AuthInfo::fromRequest ([397fd1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/397fd19eab91bb57eefe1fbd7a9b78a7019c5cc4))

##### Authorization

* Fix params ([1f7cbe](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1f7cbe32a213fa1e5d4412f855a4576c315d50a5))

##### Country

* Fix get name by language ([125baa](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/125baad4ca4a7fcfb4835e27ae7a3e521176c895))

##### Criteria

* Cast as text in order by criteria ([3c75ba](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3c75ba650f25e01bc6396afdd322173539c1e64f))

##### Doctrine

* Close connections explicitly in fetch and iterative methods ([0df6df](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0df6df08ef06617ad3ada02623ceb15328ba17dd))

##### Dumper

* The missing 'use' clauses has been included ([e7eef6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e7eef657d86fd2002358bad538b1652e72138adc))

##### Error

* Response with controlled exceptions in syncHandle processes ([63310b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/63310b97d07b9afaca3d004559d80b608ea30b82))

##### Error- Handler

* Expect Throwable instead of Exception ([efa1e8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/efa1e8b49046e1ca80b32ecfb8a20319b98747f5))

##### Error-handling

* Fix an error collecting error from workers ([1ada9d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1ada9d0db42e2f8b2c336f0e9cf4e4467d8543d1))

##### Filter Criteria

* Check if displayValue is an array to return the first value on it ([52bf10](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/52bf108daa2ff065b28fed72fcc93ddd2fe6b629))
* Fix error if total results is 0 ([807cc7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/807cc71aec5a58f7d1c79095f15ed5ac0339c33f))
* Fixes a bug which getNestedValue returned an array of values instead an only value ([e8c4bd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e8c4bd720638ceea8a8d0172ab347b2826e14147))
* Fix warning ([770fd7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/770fd755b3c2e9ca6c85a6130df4adc2dafb21a5))
* If a value was not a string, checking if it is range of values failed ([4adf54](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4adf544da62b7eab3f7b01ebfb5052765122760e))

##### Filter-and-query

* Fix filters with integers and values in ranges. Fix displayValue that was shown as array ([a18da0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a18da00d5c1cc00efff9ce2109da4dac908b96ce))

##### Iso31661-V Os

* Modidy constant values ([c00992](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c009920079b8a8b29d61c817317bef9493f369b0))

##### Iso6391 Code

* Convert value to uppercase by default ([83479b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/83479b3a8c0190d6dcf98b378622b2f6b3625de7))

##### Language Data Provider

* Convert all values to uppercase ([fb8576](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fb85765c934bf8424a684c8a87f2d758423f8d9b))

##### Location Entity

* Leave method signature like in its parent class ([3f8f33](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3f8f330ed1ebc276a99e67d6f5db7bc88a54dc4e))

##### Message Processor

* Fix wrong variable ([381162](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3811626fcb271cc75762e0ebd7b3a8e151140539))

##### Pagination

* Fix calculate total count in metadata ([f855a8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f855a8d9be2f8a455828877bdb59a110ed0e2d09))

##### Query Criteria

* Wip: Remove validation rule for sorting ([e599be](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e599bea5d64f4c11f2c702c5fe36772d191669ef))

##### Redisclient

* Make it use the pool ([252828](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/252828a53b26d5e57b627a43f8ce63f22a010f5a))

##### Request

* More than one x-content-language could trigger a Array to String conversion, warning ([d31e54](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d31e543c3fd9bcfcbab6877605b15d65db971d27))

##### Test

* Mock abstract classes for tests ([34e11a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/34e11a706f7cd9de16b3f42433c0480e30f7ee83))

##### Writes

* Add forgotten execute() method ([819869](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/819869998177802c0b069d59207cdeb58e776e74))


---

## [1.0.0-dev](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/compare/2.11.6...1.0.0-dev) (2023-07-24)

### Features

* Adaptar FeesCalculator para utilizar RedisAdapter (#9) ([985dd8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/985dd888b58694b0073afb8f19d69fcad40342c6))
* Add Attribute Route to declare Routes/Endpoints ([2e35cb](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2e35cb9949a7b84e8ac443f066723f91e7621e2a))
* Add Basic authentication to Elastic Search ([903dfe](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/903dfe3b2c548b8e09a49407b821235ab8a72096))
* Add changes to several classes ([75e202](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/75e20202edb988a2bb716099a27b6a52483324cd))
* Add Criteria to Query Builder ([999c2f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/999c2f9a8c76523487328e411fedea8f1bdfb57f))
* Add default limit to queries with QueryBuilder if not present ([a7168a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a7168a5f20c4a7432dc1ec999d5e44c9aa8b9e5b))
* Add ElasticSearch Adapter / Client ([cc0c53](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/cc0c539d5652795195e5aa32bd4b1084b66249ee))
* Add ElasticSearch Dependency ([39597d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/39597d1c4cbdc383461ac882c0a867c866c9d762))
* Add Immutable unaccent to filter criteria ([d66158](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d6615844cd1d4dd54256c1ddb78bd048ef068b11))
* Add kosovo codes (#8) ([5c0e02](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5c0e02e52abfb4c6d20e9c7734466554294912bb))
* Add lower and unaccent to criteria filter, converting all fields to text. ([c73535](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c73535d364b80bcb02362bf6331cf0d1bfd83fc7))
* Add method to retrieve query params in Request ([041cf6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/041cf61a7466f903dd007d603ac762ea336fc7b2))
* Add MultiLingualText Class ([d0eb92](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d0eb925f32aaf4ef45abcea920503ff8e2f2e661))
* Add PostFilterDto to normalise filter querystring parameters. Add hash method to CriteriaDto and add Map dependency ([9a6186](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9a6186a0933fb82c22dcf63e4cf464939fa37c9f))
* Add service providers (#13) ([10a1b6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/10a1b62485b2bc04038b826b99d9a66322e92c5f))
* Add setMultiLang method in Doctrine QueryBuilder ([aae61c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/aae61ce62a18504ebf50111fac4c793a09a4dc42))
* Add toArrayWithLangs to return all languages in address related objects ([aec31b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/aec31bb59572cdb6c275041339299c606d19392f))
* Change Method Names, remove get string from getters ([e95b3e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e95b3eb76ea1d6a2cb639600e9055be0c4621f69))
* Change QueryBuilder to Doctrine, and adapt Criteria Query to regular and jsonb columns ([d7f9ff](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d7f9ff0eedf86c9e0714bbe218d267aebde37caf))
* Dump server (#16) ([070782](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/070782174ddd097822b6cd9314edca5085b45674))
* Include method in querybuilder to retrieve fallback language if requested language do not exist. ([da1e7f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/da1e7fe725d4ec96a584cd8a998de8217e057476))
* Make private client in ElasticSearchAdapter in order to be inaccesible ([836353](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/836353e7c7e6a771000fdd71b781d810e5f80cc5))
* Write logs and error functions to stdout in non-local envs ([39d0d8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/39d0d8823a474413e7f8925017e712517f988e00))

##### Authentication

* Revisit Authentication and Authorization functionalities ([591e09](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/591e0968f4c64f1ebca7ba5787ba56ff0343e334))

##### Autocomplete

* Add Autocomplete capabilities. Author: emalmierca ([9a53b2](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9a53b239c9cdbff0808454a63fb5b188cf620f22))

##### Check Health

* Add parallel health checks and timeout ([11a6c1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/11a6c17d6cb742dbe2cd5f31ce5e189e39c16294))

##### Command Bus

* Improve Async Coroutine ([d451ed](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d451edfc39edd269aeb47ddfdd5e2c359af99043))

##### Container

* Add when method to Container ([731ce1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/731ce1cb1b46690c13ff16afd58c649d1e6c6b08))

##### Coordinates

* Add Coordinates VO and tests ([93480c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/93480c2303db2e3e673ec0d832deefa28a09d93d))

##### Country/location

* Return '' empty string instead of null in names ([38f6f7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/38f6f79f904cd078509da3f7b7b6c70c85c07fa4))

##### Criteria

* Allow use of array of search params in Criteria ([b19a34](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/b19a349f37304e48a3c6f2a481068f481fa39cdc))
* Distinguish between strings/non-strings values to apply different where clauses ([9f8988](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9f898840201b2ef3df6c99795796ebe2773ecd2b))
* Unify normal and jsonb type columns under the same method: addQueryCriteria() ([e9c0ea](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e9c0eaabe81203f32857523b0e83436ca95df22d))

##### Criteria-and-filter

* Creación de clases QueryCriteria y FilterCriteria ([faf643](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/faf643108243388f5decc46466da4e021e0b5465))

##### Crontab

* Add scheduled class execution using attribute Crontab ([6eb6e0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6eb6e080f77b20d4a709e2edf16b49e17f65c0d1))

##### Date Time

* Add DateTime ValueObject ([5ef623](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5ef6238ec21b7317952817bfbed47ff5f24aa2e2))

##### Doctrine Query Builder

* Add methods iterateAssociative, iterateColumn, iterateKeyValue to received results as Generators ([7987d9](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7987d9d25ce5a2744a079cf42262f0ee273a7182))

##### Error Handling

* Improve Handle error in app for sync and async ([671d43](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/671d43b4d6aeeef95787e38315903d7f5f67a5d8))

##### Exceptions

* Add CustomException to be extended ([0a1a61](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0a1a616286e74fc7be2cfd9b58c1a312d061089d))

##### Filter Criteria

* Add post filtering class ([e56977](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e5697744730f14468c58946f86c7befad4c6aace))
* Modify response with min and max attributes for range filters ([fa1004](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fa1004cb3ee46c9f9ddcdf9df023ad38e8307934))

##### Health

* Add CheckHealthNoDB ([f51098](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f51098fc53ef567c505d78dc133a05980bf3cac2))
* Check read and write DB, Elastic and Redis connections ([76ced2](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/76ced2513660b199fdfdb33452a5b5876da6c1b1))

##### Httpclient

* Add streamWrapper method to HttpClient ([8265bd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8265bdf93bbe3bbab36a784a195127ce604078b7))

##### Language

* Add Language VO and emthod get lang in kernel ([b002a3](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/b002a3fe63c22b6904ae3e20caaf36cdd165f494))
* Add local as accepted_language, only because it is something interesting :P ([06ce9f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/06ce9fcffb358e9077fded63953046b9933846e3))

##### Library

* Add nikic/iter library to work with iteration primitives using generators ([0abe87](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0abe8762b010cd11b9426ba5254f356494af93a3))

##### Locations Service

* Add searchSimilarLocation functionality ([7ca26a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7ca26ae9cd1d8fec6b2c90b6983dac525d5592a0))

##### Master- Common- Services

* Add Services to retrieve Countriies from ElasticSearch ([55575e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/55575e790dd194babc10736b4a24bf0ee47dfeae))

##### Masters

* Add Get Airport By IATA Service ([898d95](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/898d95357a50345cc19ca0e431a31dbd26597555))
* Add Support Service to retrieve information about Location in master domain ([7ead98](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7ead9887e8aa8fb6f610faafc150ccc2a057d4d8))

##### Messaging

* Control errors processing received messages from broker ([0b3200](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0b32005a99d303f60ac94123eb60a0d48816f5a4))

##### Migrations

* Add Doctrine Migrations dependency ([867fae](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/867fae5166c4c3b10851d2227b085eb7c15bc2a7))

##### Pagination

* Improve exception message for clients ([0c300d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0c300db5cc874b3a2f663cf670bfce954c1a44aa))

##### Person Name

* Add toArray method ([9cbc13](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9cbc13ad765bf6ae6f051c1ce1bd291ec6afa8a6))

##### Phone

* Add Phone and PhonePrefix ValueObjects ([46f28c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/46f28c580f68cce9ee8374cb225795d40477413f))

##### Query Builder

* Add executeQuery method ([5a62fd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5a62fd5360be390d53e7a56e73299400e192c3ef))

##### Query Criteria

* Added support to use QueryCriteria with DoctrineQueryBuilder ([85afab](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/85afab54acaf5c89db6a3e51106f41483f19e3f9))
* Add method to be able to include any param/value from any source such as a dynamic URI ([873210](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8732107b9a53a65a7f2f3042835ad2b5b3206108))
* Create QueryCriteria objecto in order to parse all incoming parameters and filters from client http ([3c4ba7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3c4ba79b1306d64b0ae1ae1b0e450a7a909f1c6e))

##### Querybuilder

* Add individual parameter to querybuilder ([24b035](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/24b035273d44129306b590a08f386a60cc1066eb))
* Add type params to bindValues ([8ccd49](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8ccd49e9758feeb10162d5810cf63e24a57d6884))
* Allow including types in params in setParamaters ([a4b6e1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a4b6e1e74e4941253d0d9c352345d4a8d4a3f84e))

##### Redis

* Add exists, hExists, expire y raw methods in Redis Client ([440a57](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/440a57102adedd856c7dc9540c8c9929fa6fd666))
* Add new RedisAdapter class that will replace RedisClient ([203fe1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/203fe17d208b271dc587e6aff63962158228bfbc))
* Improve transaction methods ([836096](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/836096686c0e9f4102b790c7a227b7ca823c1a8d))

##### Redis Adapter

* Add method ttl and add options to set to allow EX/NX params as array ([ea16c0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/ea16c0690acd577496d9c453422ba3b37e210d0c))

##### Redis Table

* Add insertMultiple in order to add an array of items ([2491d0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2491d064d2b6a8565a455847eff7fbd6b4b81eea))
* Add RedisTable Class to ease working and filtering datasets in a more "sql table" way ([bd89d4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/bd89d498a638b11690334a0437493a6eec5ab173))
* Improve class ([9544ac](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9544acb32136d5b2da8061ac23d0b2e8c1ad0dbb))

##### Redis-conneciton

* Add password if exists ([f7837d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f7837d79684b788436d7f7c42881351a4fd74fb9))

##### Request

* Add TenantId getter from request ([c69d7c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c69d7cf26ce4a38f5e3139f9c6f61cf3654d21ee))

##### Response

* Add headers with commit and tag version to responses ([6a325d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6a325d66c84ffe38c59e00ad354034115f46071a))
* Add methods make Repone configurable and tests ([e068d1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e068d1421c222f8fdf2ab69677bc444748910e10))
* Response:json now converts all attributes to snake_case ([fa91bf](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fa91bfa391d893a08d44bc6bcdbe520b0de721f3))

##### Sentry

* Add Sentry to kernel ([d001f8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d001f850a01b5c569a2e4f15146d9b149d1ac570))

##### Subscribing

* Classes can now subscribe to events with PHP Attributes ([0e576c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0e576c3e94d5612108e506275d37e9eb729b47fb))

##### URL

* Create Url base VO ([e8bd59](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e8bd5986b57c9356926d013dd7a1382e040a4e51))

##### UUID

* Upgrade to UUID V7 ([8af8f6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8af8f6d66eb638f8a39d969d0a278be6584de113))

##### Value Objects

* Add CancellationPolicy, IATA, and add methods to DateTime ([4babfa](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4babfa1ad167f07c33b10a3431aa8eb29b784c51))

### Bug Fixes

* Add dev errors to dev output only in debug mode ([1f7c76](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1f7c769e273d96ee209884edecd5af79c77db78d))
* Add equals method to some VOs wrongly removed ([5982e4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5982e4690027c0960d9197181aaaa6fed2ad14ae))
* Add Global Container Dependency injection in HandleHttp ([fbb3ab](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fbb3ab7e5687fcd867ba5f50170531415cb0e168))
* Add null to return type option ([2cd426](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2cd42672430b028cbec4ed4979a3b38dcfdc0a01))
* Add use of couroutines in JobScheduler process ([6eb8fc](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6eb8fc7a724ada2444f12f91cb4b4595b4f0a6aa))
* Add variadic ([f6986e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f6986e100f1f5b145ea93deb969cb11433d5b991))
* Attribute parsing redeclares classes triggering php errors ([7fe298](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7fe2989adafd58a925dca74c872190ddf0c722c9))
* Avoid invoking connection before established ([4ea548](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4ea54836ba04b7455ce30c06e9ab630606578cda))
* Change Interface isEventSourced reconstituteFrom return type ([2d38df](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2d38dfca96ab7972a6aba3301651097762daf70c))
* Change param name to be coherent ([9db16a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9db16af5d1d9dcdc1299c27665b7cce053b2a516))
* Check if accept-language is not present ([36ff16](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/36ff16c715a2e636f92e9be6fdef35c8049dd16a))
* CriteriaExtended case insensitive search ([bd3c34](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/bd3c34c03c061f16c5840e377629bdbcf519d117))
* CriteriaExtended have a unique where ([c7a45b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c7a45bfd55a39552875e2b31ddf917dff1e0efbd))
* Error parsing and replacing binding variables like name and name2 ([5b3b46](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5b3b46ba82456a65303d499069556a330f4f3720))
* Fix adding routes both by attributes and provider ([781ca7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/781ca710c67c6e2fdf400b1ab9df83371a86e381))
* Fix addressDTO if coordinates are null ([591d8f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/591d8f2f9cfe14aa3f7e868727f49649f15ac122))
* Fix base_path function and global variable ([fee5a7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fee5a7774360a0303d0bbfb46e24358bbdff6639))
* Fix illegal operation in params in Logger ([486962](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4869628ea02c1f44430c84293143857669c7a39f))
* Fix namespace for PersistenceException ([02a59a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/02a59a33c4431a605d9371c6491e27b492b0630d))
* Fix null object pattern in LocationEntity and CountryEntity ([114e11](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/114e11b7b30342abe26284ac72d1bd484ece09e0))
* Fix number of sprintf params ([eb89ba](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/eb89ba9aa0407b96ce89ff40bd5da60c86840511))
* Fix return type ([ed77cf](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/ed77cfae6282b0a2a379d0b705c63fc45f355751))
* Fix routes in logger ([3f0f6a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3f0f6acb212c6924dcd3cc291d1ebf33e46edc1a))
* Fix two bugs in SqlQueryBuilder and PostgreSQLEventStoreAdapter ([01367e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/01367e1dda35c372245ef775276453be15822b69))
* Fix type ([352547](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3525475dd621b1119406785e1bcda19e6461bac4))
* Fix typo in query ([a53948](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a53948764c7d978d09ff1ff59308389e7fe964de))
* Fix typos in routes generated by automatic refactor ([76e066](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/76e0665b1c1c538bd114758333dd1b596b5ec04c))
* Fix value visibility ([0a41fb](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0a41fb9304abbdf5f75276113820ea13da2fb23d))
* Fix wrong namespaces ([77578f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/77578f8c4e28ab5dc6113f158619a677166d11c3))
* Improve and fix Authorizations ([d37a19](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d37a191b2f9382e964d523c56dbc3b115112cc58))
* Improve index creation in ES ([c597d4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c597d4766f1ce24b9210805236c3f622b28ac67f))
* Invoke AuthorizationsMiddleware with getInstance as it is a singleton ([cadc41](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/cadc411a4079ce3594da1bd70133ae3ef7e7b696))
* Map Static values in AuthorizationsProvider Enum from strings ([5f1fb5](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5f1fb5a09ad813f038b1185e7e5783e8d6890f60))
* Modify fromScalars params to make them actual scalars ([811707](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8117070d7b329b9fa1adae8e91f397139fc8789c))
* MultiLingualText Namespace ([c19001](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c190013f67d430089617716a087c977810970e6d))
* OrderBy ([317f95](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/317f9547ed5fd3e771a54dfce416e195e8f47d47))
* QueryBuilder package folder route ([4ccf85](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4ccf85c423283b90e15de4cd6466cbb849549a2f))
* Recurrent autoinvoke in transactionId getter ([62e790](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/62e790e86ff6504bd715a5f4521eb79cfda3de61))
* Remove bug line ([720ddc](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/720ddc7bdd100edfa59b5c4072ab7d7425923ae1))
* Remove permission ([62bea0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/62bea0e6f4578dcf750dabc3b3f6521807a7c315))
* Removing from in paging query when there are no results caused an error in some edge cases ([00113b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/00113bcb5de44f543db91cea122b98e20f305a21))
* Request acceptLanguage may break with no header ([d629b1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d629b10c7a3aa28fc9abe4e802c8f805f724edff))
* Response error in syncHandle ([9926ea](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9926ea266136816a30f200bb8971e6305c8b48a0))
* Return empty array if Redis returns false in json gets ([c7e4c4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c7e4c49183447d2e23451efd8711552ff1e47829))
* Return null if key is not found by redis in json get methods ([56eca3](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/56eca3306bf56c53a07eab695c8b64b128b2f03c))

##### Address Complete Dto

* Fix error with country names ([88ae5b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/88ae5b35bf99a2bf6128cc7ce9cbabfd76a2f5d6))

##### Airport Dto

* Fix method invokation du to changes in kernel ([6be962](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6be962bb3d197099aa428d5995fe8ca3270e55c7))

##### Authinfo

* Fix differents types into one to be passed to AuthInfo::fromRequest ([397fd1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/397fd19eab91bb57eefe1fbd7a9b78a7019c5cc4))

##### Authorization

* Fix params ([1f7cbe](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1f7cbe32a213fa1e5d4412f855a4576c315d50a5))

##### Country

* Fix get name by language ([125baa](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/125baad4ca4a7fcfb4835e27ae7a3e521176c895))

##### Criteria

* Cast as text in order by criteria ([3c75ba](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3c75ba650f25e01bc6396afdd322173539c1e64f))

##### Doctrine

* Close connections explicitly in fetch and iterative methods ([0df6df](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0df6df08ef06617ad3ada02623ceb15328ba17dd))

##### Dumper

* The missing 'use' clauses has been included ([e7eef6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e7eef657d86fd2002358bad538b1652e72138adc))

##### Error

* Response with controlled exceptions in syncHandle processes ([63310b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/63310b97d07b9afaca3d004559d80b608ea30b82))

##### Error- Handler

* Expect Throwable instead of Exception ([efa1e8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/efa1e8b49046e1ca80b32ecfb8a20319b98747f5))

##### Error-handling

* Fix an error collecting error from workers ([1ada9d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1ada9d0db42e2f8b2c336f0e9cf4e4467d8543d1))

##### Filter Criteria

* Check if displayValue is an array to return the first value on it ([52bf10](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/52bf108daa2ff065b28fed72fcc93ddd2fe6b629))
* Fix error if total results is 0 ([807cc7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/807cc71aec5a58f7d1c79095f15ed5ac0339c33f))
* Fixes a bug which getNestedValue returned an array of values instead an only value ([e8c4bd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e8c4bd720638ceea8a8d0172ab347b2826e14147))
* Fix warning ([770fd7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/770fd755b3c2e9ca6c85a6130df4adc2dafb21a5))
* If a value was not a string, checking if it is range of values failed ([4adf54](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4adf544da62b7eab3f7b01ebfb5052765122760e))

##### Filter-and-query

* Fix filters with integers and values in ranges. Fix displayValue that was shown as array ([a18da0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a18da00d5c1cc00efff9ce2109da4dac908b96ce))

##### Iso31661-V Os

* Modidy constant values ([c00992](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c009920079b8a8b29d61c817317bef9493f369b0))

##### Iso6391 Code

* Convert value to uppercase by default ([83479b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/83479b3a8c0190d6dcf98b378622b2f6b3625de7))

##### Language Data Provider

* Convert all values to uppercase ([fb8576](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fb85765c934bf8424a684c8a87f2d758423f8d9b))

##### Location Entity

* Leave method signature like in its parent class ([3f8f33](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3f8f330ed1ebc276a99e67d6f5db7bc88a54dc4e))

##### Message Processor

* Fix wrong variable ([381162](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3811626fcb271cc75762e0ebd7b3a8e151140539))

##### Pagination

* Fix calculate total count in metadata ([f855a8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f855a8d9be2f8a455828877bdb59a110ed0e2d09))

##### Query Criteria

* Wip: Remove validation rule for sorting ([e599be](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e599bea5d64f4c11f2c702c5fe36772d191669ef))

##### Redisclient

* Make it use the pool ([252828](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/252828a53b26d5e57b627a43f8ce63f22a010f5a))

##### Request

* More than one x-content-language could trigger a Array to String conversion, warning ([d31e54](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d31e543c3fd9bcfcbab6877605b15d65db971d27))

##### Test

* Mock abstract classes for tests ([34e11a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/34e11a706f7cd9de16b3f42433c0480e30f7ee83))

##### Writes

* Add forgotten execute() method ([819869](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/819869998177802c0b069d59207cdeb58e776e74))


---

## [1.0.0-dev](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/compare/2.11.6...1.0.0-dev) (2023-07-24)

### Features

* Adaptar FeesCalculator para utilizar RedisAdapter (#9) ([985dd8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/985dd888b58694b0073afb8f19d69fcad40342c6))
* Add Attribute Route to declare Routes/Endpoints ([2e35cb](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2e35cb9949a7b84e8ac443f066723f91e7621e2a))
* Add Basic authentication to Elastic Search ([903dfe](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/903dfe3b2c548b8e09a49407b821235ab8a72096))
* Add changes to several classes ([75e202](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/75e20202edb988a2bb716099a27b6a52483324cd))
* Add Criteria to Query Builder ([999c2f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/999c2f9a8c76523487328e411fedea8f1bdfb57f))
* Add default limit to queries with QueryBuilder if not present ([a7168a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a7168a5f20c4a7432dc1ec999d5e44c9aa8b9e5b))
* Add ElasticSearch Adapter / Client ([cc0c53](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/cc0c539d5652795195e5aa32bd4b1084b66249ee))
* Add ElasticSearch Dependency ([39597d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/39597d1c4cbdc383461ac882c0a867c866c9d762))
* Add Immutable unaccent to filter criteria ([d66158](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d6615844cd1d4dd54256c1ddb78bd048ef068b11))
* Add kosovo codes (#8) ([5c0e02](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5c0e02e52abfb4c6d20e9c7734466554294912bb))
* Add lower and unaccent to criteria filter, converting all fields to text. ([c73535](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c73535d364b80bcb02362bf6331cf0d1bfd83fc7))
* Add method to retrieve query params in Request ([041cf6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/041cf61a7466f903dd007d603ac762ea336fc7b2))
* Add MultiLingualText Class ([d0eb92](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d0eb925f32aaf4ef45abcea920503ff8e2f2e661))
* Add PostFilterDto to normalise filter querystring parameters. Add hash method to CriteriaDto and add Map dependency ([9a6186](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9a6186a0933fb82c22dcf63e4cf464939fa37c9f))
* Add service providers (#13) ([10a1b6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/10a1b62485b2bc04038b826b99d9a66322e92c5f))
* Add setMultiLang method in Doctrine QueryBuilder ([aae61c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/aae61ce62a18504ebf50111fac4c793a09a4dc42))
* Add toArrayWithLangs to return all languages in address related objects ([aec31b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/aec31bb59572cdb6c275041339299c606d19392f))
* Change Method Names, remove get string from getters ([e95b3e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e95b3eb76ea1d6a2cb639600e9055be0c4621f69))
* Change QueryBuilder to Doctrine, and adapt Criteria Query to regular and jsonb columns ([d7f9ff](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d7f9ff0eedf86c9e0714bbe218d267aebde37caf))
* Dump server (#16) ([070782](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/070782174ddd097822b6cd9314edca5085b45674))
* Include method in querybuilder to retrieve fallback language if requested language do not exist. ([da1e7f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/da1e7fe725d4ec96a584cd8a998de8217e057476))
* Make private client in ElasticSearchAdapter in order to be inaccesible ([836353](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/836353e7c7e6a771000fdd71b781d810e5f80cc5))
* Write logs and error functions to stdout in non-local envs ([39d0d8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/39d0d8823a474413e7f8925017e712517f988e00))

##### Authentication

* Revisit Authentication and Authorization functionalities ([591e09](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/591e0968f4c64f1ebca7ba5787ba56ff0343e334))

##### Autocomplete

* Add Autocomplete capabilities. Author: emalmierca ([9a53b2](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9a53b239c9cdbff0808454a63fb5b188cf620f22))

##### Check Health

* Add parallel health checks and timeout ([11a6c1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/11a6c17d6cb742dbe2cd5f31ce5e189e39c16294))

##### Command Bus

* Improve Async Coroutine ([d451ed](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d451edfc39edd269aeb47ddfdd5e2c359af99043))

##### Container

* Add when method to Container ([731ce1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/731ce1cb1b46690c13ff16afd58c649d1e6c6b08))

##### Coordinates

* Add Coordinates VO and tests ([93480c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/93480c2303db2e3e673ec0d832deefa28a09d93d))

##### Country/location

* Return '' empty string instead of null in names ([38f6f7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/38f6f79f904cd078509da3f7b7b6c70c85c07fa4))

##### Criteria

* Allow use of array of search params in Criteria ([b19a34](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/b19a349f37304e48a3c6f2a481068f481fa39cdc))
* Distinguish between strings/non-strings values to apply different where clauses ([9f8988](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9f898840201b2ef3df6c99795796ebe2773ecd2b))
* Unify normal and jsonb type columns under the same method: addQueryCriteria() ([e9c0ea](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e9c0eaabe81203f32857523b0e83436ca95df22d))

##### Criteria-and-filter

* Creación de clases QueryCriteria y FilterCriteria ([faf643](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/faf643108243388f5decc46466da4e021e0b5465))

##### Crontab

* Add scheduled class execution using attribute Crontab ([6eb6e0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6eb6e080f77b20d4a709e2edf16b49e17f65c0d1))

##### Date Time

* Add DateTime ValueObject ([5ef623](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5ef6238ec21b7317952817bfbed47ff5f24aa2e2))

##### Doctrine Query Builder

* Add methods iterateAssociative, iterateColumn, iterateKeyValue to received results as Generators ([7987d9](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7987d9d25ce5a2744a079cf42262f0ee273a7182))

##### Error Handling

* Improve Handle error in app for sync and async ([671d43](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/671d43b4d6aeeef95787e38315903d7f5f67a5d8))

##### Exceptions

* Add CustomException to be extended ([0a1a61](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0a1a616286e74fc7be2cfd9b58c1a312d061089d))

##### Filter Criteria

* Add post filtering class ([e56977](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e5697744730f14468c58946f86c7befad4c6aace))
* Modify response with min and max attributes for range filters ([fa1004](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fa1004cb3ee46c9f9ddcdf9df023ad38e8307934))

##### Health

* Add CheckHealthNoDB ([f51098](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f51098fc53ef567c505d78dc133a05980bf3cac2))
* Check read and write DB, Elastic and Redis connections ([76ced2](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/76ced2513660b199fdfdb33452a5b5876da6c1b1))

##### Httpclient

* Add streamWrapper method to HttpClient ([8265bd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8265bdf93bbe3bbab36a784a195127ce604078b7))

##### Language

* Add Language VO and emthod get lang in kernel ([b002a3](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/b002a3fe63c22b6904ae3e20caaf36cdd165f494))
* Add local as accepted_language, only because it is something interesting :P ([06ce9f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/06ce9fcffb358e9077fded63953046b9933846e3))

##### Library

* Add nikic/iter library to work with iteration primitives using generators ([0abe87](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0abe8762b010cd11b9426ba5254f356494af93a3))

##### Locations Service

* Add searchSimilarLocation functionality ([7ca26a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7ca26ae9cd1d8fec6b2c90b6983dac525d5592a0))

##### Master- Common- Services

* Add Services to retrieve Countriies from ElasticSearch ([55575e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/55575e790dd194babc10736b4a24bf0ee47dfeae))

##### Masters

* Add Get Airport By IATA Service ([898d95](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/898d95357a50345cc19ca0e431a31dbd26597555))
* Add Support Service to retrieve information about Location in master domain ([7ead98](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7ead9887e8aa8fb6f610faafc150ccc2a057d4d8))

##### Messaging

* Control errors processing received messages from broker ([0b3200](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0b32005a99d303f60ac94123eb60a0d48816f5a4))

##### Migrations

* Add Doctrine Migrations dependency ([867fae](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/867fae5166c4c3b10851d2227b085eb7c15bc2a7))

##### Pagination

* Improve exception message for clients ([0c300d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0c300db5cc874b3a2f663cf670bfce954c1a44aa))

##### Person Name

* Add toArray method ([9cbc13](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9cbc13ad765bf6ae6f051c1ce1bd291ec6afa8a6))

##### Phone

* Add Phone and PhonePrefix ValueObjects ([46f28c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/46f28c580f68cce9ee8374cb225795d40477413f))

##### Query Builder

* Add executeQuery method ([5a62fd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5a62fd5360be390d53e7a56e73299400e192c3ef))

##### Query Criteria

* Added support to use QueryCriteria with DoctrineQueryBuilder ([85afab](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/85afab54acaf5c89db6a3e51106f41483f19e3f9))
* Add method to be able to include any param/value from any source such as a dynamic URI ([873210](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8732107b9a53a65a7f2f3042835ad2b5b3206108))
* Create QueryCriteria objecto in order to parse all incoming parameters and filters from client http ([3c4ba7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3c4ba79b1306d64b0ae1ae1b0e450a7a909f1c6e))

##### Querybuilder

* Add individual parameter to querybuilder ([24b035](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/24b035273d44129306b590a08f386a60cc1066eb))
* Add type params to bindValues ([8ccd49](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8ccd49e9758feeb10162d5810cf63e24a57d6884))
* Allow including types in params in setParamaters ([a4b6e1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a4b6e1e74e4941253d0d9c352345d4a8d4a3f84e))

##### Redis

* Add exists, hExists, expire y raw methods in Redis Client ([440a57](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/440a57102adedd856c7dc9540c8c9929fa6fd666))
* Add new RedisAdapter class that will replace RedisClient ([203fe1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/203fe17d208b271dc587e6aff63962158228bfbc))
* Improve transaction methods ([836096](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/836096686c0e9f4102b790c7a227b7ca823c1a8d))

##### Redis Adapter

* Add method ttl and add options to set to allow EX/NX params as array ([ea16c0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/ea16c0690acd577496d9c453422ba3b37e210d0c))

##### Redis Table

* Add insertMultiple in order to add an array of items ([2491d0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2491d064d2b6a8565a455847eff7fbd6b4b81eea))
* Add RedisTable Class to ease working and filtering datasets in a more "sql table" way ([bd89d4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/bd89d498a638b11690334a0437493a6eec5ab173))
* Improve class ([9544ac](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9544acb32136d5b2da8061ac23d0b2e8c1ad0dbb))

##### Redis-conneciton

* Add password if exists ([f7837d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f7837d79684b788436d7f7c42881351a4fd74fb9))

##### Request

* Add TenantId getter from request ([c69d7c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c69d7cf26ce4a38f5e3139f9c6f61cf3654d21ee))

##### Response

* Add headers with commit and tag version to responses ([6a325d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6a325d66c84ffe38c59e00ad354034115f46071a))
* Add methods make Repone configurable and tests ([e068d1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e068d1421c222f8fdf2ab69677bc444748910e10))
* Response:json now converts all attributes to snake_case ([fa91bf](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fa91bfa391d893a08d44bc6bcdbe520b0de721f3))

##### Sentry

* Add Sentry to kernel ([d001f8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d001f850a01b5c569a2e4f15146d9b149d1ac570))

##### Subscribing

* Classes can now subscribe to events with PHP Attributes ([0e576c](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0e576c3e94d5612108e506275d37e9eb729b47fb))

##### URL

* Create Url base VO ([e8bd59](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e8bd5986b57c9356926d013dd7a1382e040a4e51))

##### UUID

* Upgrade to UUID V7 ([8af8f6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8af8f6d66eb638f8a39d969d0a278be6584de113))

##### Value Objects

* Add CancellationPolicy, IATA, and add methods to DateTime ([4babfa](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4babfa1ad167f07c33b10a3431aa8eb29b784c51))

### Bug Fixes

* Add dev errors to dev output only in debug mode ([1f7c76](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1f7c769e273d96ee209884edecd5af79c77db78d))
* Add equals method to some VOs wrongly removed ([5982e4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5982e4690027c0960d9197181aaaa6fed2ad14ae))
* Add Global Container Dependency injection in HandleHttp ([fbb3ab](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fbb3ab7e5687fcd867ba5f50170531415cb0e168))
* Add null to return type option ([2cd426](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2cd42672430b028cbec4ed4979a3b38dcfdc0a01))
* Add use of couroutines in JobScheduler process ([6eb8fc](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6eb8fc7a724ada2444f12f91cb4b4595b4f0a6aa))
* Add variadic ([f6986e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f6986e100f1f5b145ea93deb969cb11433d5b991))
* Attribute parsing redeclares classes triggering php errors ([7fe298](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/7fe2989adafd58a925dca74c872190ddf0c722c9))
* Avoid invoking connection before established ([4ea548](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4ea54836ba04b7455ce30c06e9ab630606578cda))
* Change Interface isEventSourced reconstituteFrom return type ([2d38df](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/2d38dfca96ab7972a6aba3301651097762daf70c))
* Change param name to be coherent ([9db16a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9db16af5d1d9dcdc1299c27665b7cce053b2a516))
* Check if accept-language is not present ([36ff16](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/36ff16c715a2e636f92e9be6fdef35c8049dd16a))
* CriteriaExtended case insensitive search ([bd3c34](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/bd3c34c03c061f16c5840e377629bdbcf519d117))
* CriteriaExtended have a unique where ([c7a45b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c7a45bfd55a39552875e2b31ddf917dff1e0efbd))
* Error parsing and replacing binding variables like name and name2 ([5b3b46](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5b3b46ba82456a65303d499069556a330f4f3720))
* Fix adding routes both by attributes and provider ([781ca7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/781ca710c67c6e2fdf400b1ab9df83371a86e381))
* Fix addressDTO if coordinates are null ([591d8f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/591d8f2f9cfe14aa3f7e868727f49649f15ac122))
* Fix base_path function and global variable ([fee5a7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fee5a7774360a0303d0bbfb46e24358bbdff6639))
* Fix illegal operation in params in Logger ([486962](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4869628ea02c1f44430c84293143857669c7a39f))
* Fix namespace for PersistenceException ([02a59a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/02a59a33c4431a605d9371c6491e27b492b0630d))
* Fix null object pattern in LocationEntity and CountryEntity ([114e11](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/114e11b7b30342abe26284ac72d1bd484ece09e0))
* Fix number of sprintf params ([eb89ba](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/eb89ba9aa0407b96ce89ff40bd5da60c86840511))
* Fix return type ([ed77cf](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/ed77cfae6282b0a2a379d0b705c63fc45f355751))
* Fix routes in logger ([3f0f6a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3f0f6acb212c6924dcd3cc291d1ebf33e46edc1a))
* Fix two bugs in SqlQueryBuilder and PostgreSQLEventStoreAdapter ([01367e](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/01367e1dda35c372245ef775276453be15822b69))
* Fix type ([352547](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3525475dd621b1119406785e1bcda19e6461bac4))
* Fix typo in query ([a53948](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a53948764c7d978d09ff1ff59308389e7fe964de))
* Fix typos in routes generated by automatic refactor ([76e066](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/76e0665b1c1c538bd114758333dd1b596b5ec04c))
* Fix value visibility ([0a41fb](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0a41fb9304abbdf5f75276113820ea13da2fb23d))
* Fix wrong namespaces ([77578f](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/77578f8c4e28ab5dc6113f158619a677166d11c3))
* Improve and fix Authorizations ([d37a19](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d37a191b2f9382e964d523c56dbc3b115112cc58))
* Improve index creation in ES ([c597d4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c597d4766f1ce24b9210805236c3f622b28ac67f))
* Invoke AuthorizationsMiddleware with getInstance as it is a singleton ([cadc41](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/cadc411a4079ce3594da1bd70133ae3ef7e7b696))
* Map Static values in AuthorizationsProvider Enum from strings ([5f1fb5](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/5f1fb5a09ad813f038b1185e7e5783e8d6890f60))
* Modify fromScalars params to make them actual scalars ([811707](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/8117070d7b329b9fa1adae8e91f397139fc8789c))
* MultiLingualText Namespace ([c19001](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c190013f67d430089617716a087c977810970e6d))
* OrderBy ([317f95](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/317f9547ed5fd3e771a54dfce416e195e8f47d47))
* QueryBuilder package folder route ([4ccf85](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4ccf85c423283b90e15de4cd6466cbb849549a2f))
* Recurrent autoinvoke in transactionId getter ([62e790](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/62e790e86ff6504bd715a5f4521eb79cfda3de61))
* Remove bug line ([720ddc](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/720ddc7bdd100edfa59b5c4072ab7d7425923ae1))
* Remove permission ([62bea0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/62bea0e6f4578dcf750dabc3b3f6521807a7c315))
* Removing from in paging query when there are no results caused an error in some edge cases ([00113b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/00113bcb5de44f543db91cea122b98e20f305a21))
* Request acceptLanguage may break with no header ([d629b1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d629b10c7a3aa28fc9abe4e802c8f805f724edff))
* Response error in syncHandle ([9926ea](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/9926ea266136816a30f200bb8971e6305c8b48a0))
* Return empty array if Redis returns false in json gets ([c7e4c4](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c7e4c49183447d2e23451efd8711552ff1e47829))
* Return null if key is not found by redis in json get methods ([56eca3](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/56eca3306bf56c53a07eab695c8b64b128b2f03c))

##### Address Complete Dto

* Fix error with country names ([88ae5b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/88ae5b35bf99a2bf6128cc7ce9cbabfd76a2f5d6))

##### Airport Dto

* Fix method invokation du to changes in kernel ([6be962](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/6be962bb3d197099aa428d5995fe8ca3270e55c7))

##### Authinfo

* Fix differents types into one to be passed to AuthInfo::fromRequest ([397fd1](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/397fd19eab91bb57eefe1fbd7a9b78a7019c5cc4))

##### Authorization

* Fix params ([1f7cbe](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1f7cbe32a213fa1e5d4412f855a4576c315d50a5))

##### Country

* Fix get name by language ([125baa](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/125baad4ca4a7fcfb4835e27ae7a3e521176c895))

##### Criteria

* Cast as text in order by criteria ([3c75ba](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3c75ba650f25e01bc6396afdd322173539c1e64f))

##### Doctrine

* Close connections explicitly in fetch and iterative methods ([0df6df](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/0df6df08ef06617ad3ada02623ceb15328ba17dd))

##### Dumper

* The missing 'use' clauses has been included ([e7eef6](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e7eef657d86fd2002358bad538b1652e72138adc))

##### Error

* Response with controlled exceptions in syncHandle processes ([63310b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/63310b97d07b9afaca3d004559d80b608ea30b82))

##### Error- Handler

* Expect Throwable instead of Exception ([efa1e8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/efa1e8b49046e1ca80b32ecfb8a20319b98747f5))

##### Error-handling

* Fix an error collecting error from workers ([1ada9d](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/1ada9d0db42e2f8b2c336f0e9cf4e4467d8543d1))

##### Filter Criteria

* Check if displayValue is an array to return the first value on it ([52bf10](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/52bf108daa2ff065b28fed72fcc93ddd2fe6b629))
* Fix error if total results is 0 ([807cc7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/807cc71aec5a58f7d1c79095f15ed5ac0339c33f))
* Fixes a bug which getNestedValue returned an array of values instead an only value ([e8c4bd](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e8c4bd720638ceea8a8d0172ab347b2826e14147))
* Fix warning ([770fd7](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/770fd755b3c2e9ca6c85a6130df4adc2dafb21a5))
* If a value was not a string, checking if it is range of values failed ([4adf54](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/4adf544da62b7eab3f7b01ebfb5052765122760e))

##### Filter-and-query

* Fix filters with integers and values in ranges. Fix displayValue that was shown as array ([a18da0](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/a18da00d5c1cc00efff9ce2109da4dac908b96ce))

##### Iso31661-V Os

* Modidy constant values ([c00992](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/c009920079b8a8b29d61c817317bef9493f369b0))

##### Iso6391 Code

* Convert value to uppercase by default ([83479b](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/83479b3a8c0190d6dcf98b378622b2f6b3625de7))

##### Language Data Provider

* Convert all values to uppercase ([fb8576](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/fb85765c934bf8424a684c8a87f2d758423f8d9b))

##### Location Entity

* Leave method signature like in its parent class ([3f8f33](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3f8f330ed1ebc276a99e67d6f5db7bc88a54dc4e))

##### Message Processor

* Fix wrong variable ([381162](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/3811626fcb271cc75762e0ebd7b3a8e151140539))

##### Pagination

* Fix calculate total count in metadata ([f855a8](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/f855a8d9be2f8a455828877bdb59a110ed0e2d09))

##### Query Criteria

* Wip: Remove validation rule for sorting ([e599be](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/e599bea5d64f4c11f2c702c5fe36772d191669ef))

##### Redisclient

* Make it use the pool ([252828](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/252828a53b26d5e57b627a43f8ce63f22a010f5a))

##### Request

* More than one x-content-language could trigger a Array to String conversion, warning ([d31e54](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/d31e543c3fd9bcfcbab6877605b15d65db971d27))

##### Test

* Mock abstract classes for tests ([34e11a](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/34e11a706f7cd9de16b3f42433c0480e30f7ee83))

##### Writes

* Add forgotten execute() method ([819869](https://quadrant-gitlab.internal.com.es/quadrant/backend/boilerwork-kernel/commit/819869998177802c0b069d59207cdeb58e776e74))


---


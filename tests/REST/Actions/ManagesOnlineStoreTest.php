<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Cursor;
use Pactode\Shopify\REST\Resources\ApiResource;
use Pactode\Shopify\REST\Resources\ArticleResource;
use Pactode\Shopify\REST\Resources\AssetResource;
use Pactode\Shopify\REST\Resources\BlogResource;
use Pactode\Shopify\REST\Resources\PageResource;

beforeEach(fn () => $this->shopify = Factory::fromConfig());

test('it creates a redirect', function () {
    Http::fake([
        '*' => Http::response($this->fixture('redirects.create')),
    ]);

    $resource = $this->shopify->createRedirect('/ipod', '/pages/itunes');

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/redirects.json'
        && $request->data() === ['redirect' => ['path' => '/ipod', 'target' => '/pages/itunes']]
        && $request->method() === 'POST'
    );

    expect($resource)->toBeInstanceOf(ApiResource::class);
});

test('it counts redirects', function () {
    Http::fake([
        '*' => Http::response(['count' => 42]),
    ]);

    $count = $this->shopify->getRedirectsCount();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/redirects/count.json'
        && $request->method() === 'GET'
    );

    expect($count)->toBe(42);
});

test('it gets redirects', function () {
    Http::fake([
        '*' => Http::response($this->fixture('redirects.all')),
    ]);

    $resources = $this->shopify->getRedirects();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/redirects.json'
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(ApiResource::class);
    expect($resources)->toHaveCount(3);
});

test('it finds a redirect', function () {
    Http::fake([
        '*' => Http::response($this->fixture('redirects.show')),
    ]);

    $resource = $this->shopify->getRedirect($id = 1234);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/redirects/'.$id.'.json'
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(ApiResource::class);
});

test('it updates a redirect', function () {
    Http::fake([
        '*' => Http::response($this->fixture('redirects.show')),
    ]);

    $id = 1234;

    $resource = $this->shopify->updateRedirect($id, $payload = [
        'path' => '/foo',
        'target' => '/pages/bar',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/redirects/'.$id.'.json'
        && $request->data() === ['redirect' => $payload]
        && $request->method() === 'PUT'
    );

    expect($resource)->toBeInstanceOf(ApiResource::class);
});

test('it deletes a redirect', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $id = 1234;

    $this->shopify->deleteRedirect($id);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/redirects/'.$id.'.json'
        && $request->method() === 'DELETE'
    );
});

test('it paginates redirects', function () {
    Http::fakeSequence()
        ->push(['count' => 6], 200)
        ->push($this->fixture('redirects.all'), 200, ['Link' => '<'.$this->shopify->getBaseUrl().'/redirects.json?page_info=1234&limit=2>; rel=next'])
        ->push($this->fixture('redirects.all'), 200);

    $count = $this->shopify->getRedirectsCount();
    $pages = $this->shopify->paginateRedirects(['limit' => 2]);

    $results = collect();

    foreach ($pages as $page) {
        $results = $results->merge($page);
    }

    expect($pages)->toBeInstanceOf(Cursor::class);
    expect($results->count())->toBe($count);

    Http::assertSequencesAreEmpty();
});

test('it creates a blog', function () {
    Http::fake([
        '*' => Http::response($this->fixture('blogs.create')),
    ]);

    $resource = $this->shopify->createBlog($payload = [
        'title' => 'Apple main blog',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/blogs.json'
        && $request->data() === ['blog' => $payload]
        && $request->method() === 'POST'
    );

    expect($resource)->toBeInstanceOf(BlogResource::class);
});

test('it counts blogs', function () {
    Http::fake([
        '*' => Http::response(['count' => 42]),
    ]);

    $count = $this->shopify->getBlogsCount();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/blogs/count.json'
        && $request->method() === 'GET'
    );

    expect($count)->toBe(42);
});

test('it gets blogs', function () {
    Http::fake([
        '*' => Http::response($this->fixture('blogs.all')),
    ]);

    $resources = $this->shopify->getBlogs();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/blogs.json'
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(BlogResource::class);
    expect($resources)->toHaveCount(2);
});

test('it finds a blog', function () {
    Http::fake([
        '*' => Http::response($this->fixture('blogs.show')),
    ]);

    $resource = $this->shopify->getBlog($id = 1234);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/blogs/'.$id.'.json'
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(BlogResource::class);
});

test('it updates a blog', function () {
    Http::fake([
        '*' => Http::response($this->fixture('blogs.show')),
    ]);

    $id = 1234;

    $resource = $this->shopify->updateBlog($id, $payload = [
        'title' => 'IPod Updates',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/blogs/'.$id.'.json'
        && $request->data() === ['blog' => $payload]
        && $request->method() === 'PUT'
    );

    expect($resource)->toBeInstanceOf(BlogResource::class);
});

test('it deletes a blog', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $id = 1234;

    $this->shopify->deleteBlog($id);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/blogs/'.$id.'.json'
        && $request->method() === 'DELETE'
    );
});

test('it paginates blogs', function () {
    Http::fakeSequence()
        ->push(['count' => 4], 200)
        ->push($this->fixture('blogs.all'), 200, ['Link' => '<'.$this->shopify->getBaseUrl().'/blogs.json?page_info=1234&limit=2>; rel=next'])
        ->push($this->fixture('blogs.all'), 200);

    $count = $this->shopify->getBlogsCount();
    $pages = $this->shopify->paginateBlogs(['limit' => 2]);

    $results = collect();

    foreach ($pages as $page) {
        $results = $results->merge($page);
    }

    expect($pages)->toBeInstanceOf(Cursor::class);
    expect($results->count())->toBe($count);

    Http::assertSequencesAreEmpty();
});

test('it creates a page', function () {
    Http::fake([
        '*' => Http::response($this->fixture('pages.show')),
    ]);

    $resource = $this->shopify->createPage($payload = [
        'title' => 'Apple main page',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/pages.json'
        && $request->data() === ['page' => $payload]
        && $request->method() === 'POST'
    );

    expect($resource)->toBeInstanceOf(PageResource::class);
});

test('it counts pages', function () {
    Http::fake([
        '*' => Http::response(['count' => 42]),
    ]);

    $count = $this->shopify->getPagesCount();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/pages/count.json'
        && $request->method() === 'GET'
    );

    expect($count)->toBe(42);
});

test('it gets pages', function () {
    Http::fake([
        '*' => Http::response($this->fixture('pages.all')),
    ]);

    $resources = $this->shopify->getPages();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/pages.json'
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(PageResource::class);
    expect($resources)->toHaveCount(4);
});

test('it finds a page', function () {
    Http::fake([
        '*' => Http::response($this->fixture('pages.show')),
    ]);

    $resource = $this->shopify->getPage($id = 1234);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/pages/'.$id.'.json'
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(PageResource::class);
});

test('it updates a page', function () {
    Http::fake([
        '*' => Http::response($this->fixture('pages.show')),
    ]);

    $id = 1234;

    $resource = $this->shopify->updatePage($id, $payload = [
        'title' => 'IPod Updates',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/pages/'.$id.'.json'
        && $request->data() === ['page' => $payload]
        && $request->method() === 'PUT'
    );

    expect($resource)->toBeInstanceOf(PageResource::class);
});

test('it deletes a page', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $id = 1234;

    $this->shopify->deletePage($id);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/pages/'.$id.'.json'
        && $request->method() === 'DELETE'
    );
});

test('it paginates pages', function () {
    Http::fakeSequence()
        ->push(['count' => 8], 200)
        ->push($this->fixture('pages.all'), 200, ['Link' => '<'.$this->shopify->getBaseUrl().'/pages.json?page_info=1234&limit=2>; rel=next'])
        ->push($this->fixture('pages.all'), 200);

    $count = $this->shopify->getPagesCount();
    $pages = $this->shopify->paginatePages(['limit' => 2]);

    $results = collect();

    foreach ($pages as $page) {
        $results = $results->merge($page);
    }

    expect($pages)->toBeInstanceOf(Cursor::class);
    expect($results->count())->toBe($count);

    Http::assertSequencesAreEmpty();
});

test('it creates an article', function () {
    Http::fake([
        '*' => Http::response($this->fixture('articles.show')),
    ]);

    $resource = $this->shopify->createArticle($payload = [
        'title' => 'My new Article title',
        'author' => 'John Smith',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles.json'
        && $request->data() === ['article' => $payload]
        && $request->method() === 'POST'
    );

    expect($resource)->toBeInstanceOf(ArticleResource::class);
});

test('it counts articles', function () {
    Http::fake([
        '*' => Http::response(['count' => 42]),
    ]);

    $count = $this->shopify->getArticlesCount();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles/count.json'
        && $request->method() === 'GET'
    );

    expect($count)->toBe(42);
});

test('it gets articles', function () {
    Http::fake([
        '*' => Http::response($this->fixture('articles.all')),
    ]);

    $resources = $this->shopify->getArticles();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles.json'
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(ArticleResource::class);
    expect($resources)->toHaveCount(4);
});

test('it gets article authors', function () {
    Http::fake([
        '*' => Http::response(['authors' => ['Foo', 'Bar']]),
    ]);

    $authors = $this->shopify->getArticleAuthors();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles/authors.json'
        && $request->method() === 'GET'
    );

    expect($authors)->toEqual(['Foo', 'Bar']);
});

test('it gets article tags', function () {
    Http::fake([
        '*' => Http::response(['tags' => ['Announcement', 'New']]),
    ]);

    $tags = $this->shopify->getArticleTags();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles/tags.json'
        && $request->method() === 'GET'
    );

    expect($tags)->toEqual(['Announcement', 'New']);
});

test('it finds an article', function () {
    Http::fake([
        '*' => Http::response($this->fixture('articles.show')),
    ]);

    $resource = $this->shopify->getArticle($id = 1234);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles/'.$id.'.json'
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(ArticleResource::class);
});

test('it updates an article', function () {
    Http::fake([
        '*' => Http::response($this->fixture('articles.show')),
    ]);

    $id = 1234;

    $resource = $this->shopify->updateArticle($id, $payload = [
        'title' => 'Some new title',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles/'.$id.'.json'
        && $request->data() === ['article' => $payload]
        && $request->method() === 'PUT'
    );

    expect($resource)->toBeInstanceOf(ArticleResource::class);
});

test('it deletes an article', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $id = 1234;

    $this->shopify->deleteArticle($id);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/articles/'.$id.'.json'
        && $request->method() === 'DELETE'
    );
});

test('it paginates articles', function () {
    Http::fakeSequence()
        ->push(['count' => 8], 200)
        ->push($this->fixture('articles.all'), 200, ['Link' => '<'.$this->shopify->getBaseUrl().'/articles.json?page_info=1234&limit=2>; rel=next'])
        ->push($this->fixture('articles.all'), 200);

    $count = $this->shopify->getArticlesCount();
    $pages = $this->shopify->paginateArticles(['limit' => 2]);

    $results = collect();

    foreach ($pages as $page) {
        $results = $results->merge($page);
    }

    expect($pages)->toBeInstanceOf(Cursor::class);
    expect($results->count())->toBe($count);

    Http::assertSequencesAreEmpty();
});

test('it gets assets', function () {
    Http::fake([
        '*' => Http::response($this->fixture('assets.all')),
    ]);

    $themeId = 'test-theme';

    $resources = $this->shopify->getAssets($themeId);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/themes/'.$themeId.'/assets.json'
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(AssetResource::class);
    expect($resources)->toHaveCount(27);
});

test('it finds an asset', function () {
    Http::fake([
        '*' => Http::response($this->fixture('assets.show')),
    ]);

    $themeId = 'test-theme';
    $assetKey = 'assets/bg-body.gif';

    $resource = $this->shopify->getAsset($themeId, $assetKey);

    Http::assertSent(fn (Request $request) => urldecode($request->url()) === $this->shopify->getBaseUrl().'/themes/'.$themeId.'/assets.json?asset[key]='.$assetKey
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(AssetResource::class);
});

test('it updates an asset', function () {
    Http::fake([
        '*' => Http::response($this->fixture('assets.show')),
    ]);

    $themeId = 'test-theme';
    $assetKey = 'assets/bg-body.gif';

    $resource = $this->shopify->updateAsset($themeId, $payload = [
        'key' => $assetKey,
        'value' => "<img src='backsoon-postit.png'><p>We are busy updating the store for you and will be back within the hour.</p>",
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().'/themes/'.$themeId.'/assets.json'
        && $request->data() === $payload
        && $request->method() === 'PUT'
    );

    expect($resource)->toBeInstanceOf(AssetResource::class);
});

test('it deletes an asset', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $themeId = 'theme';
    $assetKey = 'assets/bg-body.gif';

    $this->shopify->deleteAsset($themeId, $assetKey);

    Http::assertSent(fn (Request $request) => urldecode($request->url()) === $this->shopify->getBaseUrl().'/themes/'.$themeId.'/assets.json?asset[key]='.$assetKey
        && $request->method() === 'DELETE'
    );
});

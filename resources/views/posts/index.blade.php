<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>

    <div
        x-data="postsCrud({ posts: @js($posts), csrfToken: @js(csrf_token()), postsBaseUrl: @js(url('/posts')) })"
        class="py-8"
    >
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div
                x-show="flash.message"
                x-transition
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm"
            >
                <span x-text="flash.message"></span>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Create New Post</h3>

                <form @submit.prevent="createPost" class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input
                            id="title"
                            type="text"
                            x-model="form.title"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Write a short post title"
                        >
                        <p x-show="errors.title" class="mt-1 text-sm text-red-600" x-text="errors.title"></p>
                    </div>

                    <div>
                        <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Body</label>
                        <textarea
                            id="body"
                            rows="4"
                            x-model="form.body"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Write your post content"
                        ></textarea>
                        <p x-show="errors.body" class="mt-1 text-sm text-red-600" x-text="errors.body"></p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 disabled:opacity-50"
                        :disabled="loading"
                    >
                        <span x-show="!loading">Create Post</span>
                        <span x-show="loading">Saving...</span>
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your Posts</h3>
                    <button
                        @click="fetchPosts"
                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline"
                        type="button"
                    >
                        Refresh
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="post in posts" :key="post.id">
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="space-y-2">
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100" x-text="post.title"></h4>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line" x-text="post.body"></p>
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <button
                                        @click="openEditModal(post)"
                                        type="button"
                                        class="rounded-md border border-gray-300 dark:border-gray-600 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deletePost(post.id)"
                                        type="button"
                                        class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-500"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <p x-show="posts.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                        You have no posts yet. Create one above.
                    </p>
                </div>
            </div>
        </div>

        <div
            x-show="editModalOpen"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
        >
            <div @click.outside="closeEditModal" class="w-full max-w-lg rounded-xl bg-white dark:bg-gray-800 p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Post</h3>
                    <button @click="closeEditModal" type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
                        X
                    </button>
                </div>

                <form @submit.prevent="updatePost" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input
                            type="text"
                            x-model="editForm.title"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <p x-show="errors.title" class="mt-1 text-sm text-red-600" x-text="errors.title"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Body</label>
                        <textarea
                            rows="4"
                            x-model="editForm.body"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>
                        <p x-show="errors.body" class="mt-1 text-sm text-red-600" x-text="errors.body"></p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-50"
                            :disabled="loading"
                        >
                            <span x-show="!loading">Update</span>
                            <span x-show="loading">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function postsCrud({ posts, csrfToken, postsBaseUrl }) {
            return {
                posts: posts ?? [],
                form: { title: '', body: '' },
                editForm: { id: null, title: '', body: '' },
                editModalOpen: false,
                loading: false,
                errors: {},
                flash: { message: '' },

                async fetchPosts() {
                    try {
                        const { data } = await axios.get(postsBaseUrl, {
                            headers: { Accept: 'application/json' },
                        });
                        this.posts = data.posts ?? [];
                    } catch (error) {
                        this.handleRequestError(error);
                    }
                },

                async createPost() {
                    this.loading = true;
                    this.errors = {};

                    try {
                        const { data } = await axios.post(postsBaseUrl, {
                            _token: csrfToken,
                            title: this.form.title,
                            body: this.form.body,
                        });

                        this.posts.unshift(data.post);
                        this.form = { title: '', body: '' };
                        this.showFlash(data.message ?? 'Post created.');
                    } catch (error) {
                        this.handleRequestError(error);
                    } finally {
                        this.loading = false;
                    }
                },

                openEditModal(post) {
                    this.errors = {};
                    this.editForm = {
                        id: post.id,
                        title: post.title,
                        body: post.body,
                    };
                    this.editModalOpen = true;
                },

                closeEditModal() {
                    this.editModalOpen = false;
                },

                async updatePost() {
                    this.loading = true;
                    this.errors = {};

                    try {
                        const { data } = await axios.patch(`${postsBaseUrl}/${this.editForm.id}`, {
                            _token: csrfToken,
                            title: this.editForm.title,
                            body: this.editForm.body,
                        });

                        this.posts = this.posts.map((post) => (post.id === data.post.id ? data.post : post));
                        this.closeEditModal();
                        this.showFlash(data.message ?? 'Post updated.');
                    } catch (error) {
                        this.handleRequestError(error);
                    } finally {
                        this.loading = false;
                    }
                },

                async deletePost(postId) {
                    if (!confirm('Delete this post?')) {
                        return;
                    }

                    try {
                        const { data } = await axios.delete(`${postsBaseUrl}/${postId}`, {
                            data: { _token: csrfToken },
                        });

                        this.posts = this.posts.filter((post) => post.id !== postId);
                        this.showFlash(data.message ?? 'Post deleted.');
                    } catch (error) {
                        this.handleRequestError(error);
                    }
                },

                handleRequestError(error) {
                    if (error.response?.status === 422) {
                        const serverErrors = error.response.data.errors ?? {};
                        this.errors = {
                            title: serverErrors.title?.[0] ?? '',
                            body: serverErrors.body?.[0] ?? '',
                        };
                        return;
                    }

                    this.showFlash(error.response?.data?.message ?? 'Something went wrong.');
                },

                showFlash(message) {
                    this.flash.message = message;
                    setTimeout(() => {
                        this.flash.message = '';
                    }, 3000);
                },
            };
        }
    </script>
</x-app-layout>

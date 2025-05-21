<template>

    <Head title="ガチャ 編集" />

    <AppLayout :closeModal="closeModal">
        <div class="pt-6 md:px-6 px-4">
            <div class="flex flex-wrap justify-between mb-4 gap-2">
                <div class="text-right">
                    <button v-if="(gacha.status == 1)" type="button" @click="toPublic(0)"
                        :class="{ 'opacity-25': form_to_public.processing }" :disabled="form_to_public.processing"
                        class="inline w-44 py-2.5 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150">
                        非公開にする
                    </button>
                    <button v-else type="button" @click="toPublic(1)"
                        :class="{ 'opacity-25': form_to_public.processing }" :disabled="form_to_public.processing"
                        class="inline w-44 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        公開にする
                    </button>
                </div>

                <div>
                    <button v-if="(gacha.gacha_limit == 1)" type="button" @click="gachaLimit(0)"
                        :class="{ 'opacity-25': form_to_limit.processing }" :disabled="form_to_limit.processing"
                        class="inline w-44 py-2.5 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150">
                        1日1回制限キャンセル
                    </button>
                    <button v-else type="button" @click="gachaLimit(1)"
                        :class="{ 'opacity-25': form_to_limit.processing }" :disabled="form_to_limit.processing"
                        class="inline w-44 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white  tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        1日1回制限設定
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit()">
                <!-- <div class="mb-4">
                    <label for="title" class="block font-medium text-sm text-neutral-700 mb-1">タイトル</label>
                    <input v-model="form.title" id="title" type="text" class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300" placeholder="入力してください"/>
                    <div v-if="errors.title" class="text-red-500 text-sm mt-1">
                        {{ errors.title }}
                    </div>
                </div> -->

                <div class="mb-4">
                    <label for="text1" class="block font-medium text-sm text-neutral-700 mb-1">消費ポイント（テキスト）<span
                            class="text-red-500">*</span></label>
                    <input v-model="form.point" id="text1" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.point" class="text-red-500 text-sm mt-1">
                        {{ errors.point }}
                    </div>
                </div>

                <!-- <div class="mb-4">
                    <label for="consume_point"
                        class="block font-medium text-sm text-neutral-700 mb-1">ランクアップポイント（テキスト）<span
                            class="text-red-500">*</span></label>
                    <input v-model="form.consume_point" id="consume_point" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.point" class="text-red-500 text-sm mt-1">
                        {{ errors.point }}
                    </div>
                </div> -->

                <!-- <div class="mb-4">
                    <label for="type" class="block font-medium text-sm text-neutral-700 mb-2 ml-1">種類 <span
                            class="text-red-500">*</span></label>
                    <select v-model="form.lost_product_type" id="type"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300">
                        <option value="0">限定カード</option>
                        <option value="1">無限ガチャ</option>
                        <option value="2">新規ユーザー限定</option>
                    </select>
                </div> -->

                <div class="mb-4">
                    <label for="text2" class="block font-medium text-sm text-neutral-700 mb-1">総カード数（半角数字）<span
                            class="text-red-500">*</span></label>
                    <input v-model="form.count_card" id="text2" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.count_card" class="text-red-500 text-sm mt-1">
                        {{ errors.count_card }}
                    </div>
                </div>

                <div class="mb-4">
                    <label for="text1" class="block font-medium text-sm text-neutral-700 mb-1">ガチャ制限回数 <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.spin_limit" id="text1" type="number"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                        placeholder="入力してください" />
                    <div v-if="errors.spin_limit" class="text-red-500 text-sm mt-1">
                        {{ errors.spin_limit }}
                    </div>
                </div>

                <!-- <div class="mb-4">
                    <label for="rank_limit" class="block font-medium text-sm text-neutral-700 mb-1">ランク制限</label>
                    <select v-model="form.rank_limit" id="type"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300">
                        <option :value="0">制限なし</option>
                        <template v-for="(rank, index) in ranks">
                            <option :value="rank.rank">{{ rank.title }}</option>
                        </template>
                        <option :value="-1">シークレット</option>
                    </select>
                </div> -->

                <!-- <div class="mb-4">
                    <label for="plan_limit" class="block font-medium text-sm text-neutral-700 mb-1">サブスクリプション制限</label>
                    <select v-model="form.plan_limit" id="type"
                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300">
                        <option :value="0">制限なし</option>
                        <template v-for="(plan, index) in plans">
                            <option :value="plan.id">{{ plan.name }}</option>
                        </template>
                        <option :value="-1">サブスクリプション</option>
                    </select>
                </div> -->

                <!-- <div class="mb-4">
                    <label class="block font-medium text-sm text-neutral-700 mb-2 ml-1">開始時刻</label>
                    <div class="w-full flex flex-wrap gap-3 items-center">
                        <VueDatePicker v-model="form.start_time" format="Y-MM-dd HH:mm" class="flex-1"></VueDatePicker>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-sm text-neutral-700 mb-2 ml-1">終了時刻</label>
                    <div class="w-full flex flex-wrap gap-3 items-center">
                        <VueDatePicker v-model="form.end_time" format="Y-MM-dd HH:mm" class="flex-1"></VueDatePicker>
                    </div>
                </div> -->

                
                <div class="mb-4">
                    <label for="cards"
                        class="block font-medium text-sm text-neutral-700 mb-2 ml-1">ハズレカード登録（半角数字）</label>
                    <template v-for="(item, key) in form.lostProducts">
                        <div v-if="item.key" class="w-full flex flex-row items-center mb-3">
                            <div class="flex-1 flex items-center">
                                <div class="flex-1 inline-block pr-1">
                                    <input v-model="item.point" type="number"
                                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                        placeholder="変換pt" />
                                </div>
                                <span class="pl-1 pr-3">PT : </span>
                                <div class="flex-1 inline-block pr-1">
                                    <input v-model="item.count" type="number"
                                        class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                        placeholder="枚数" :step="count_step" />
                                </div>
                                <span class="px-2">{{ count_step == 1 ? '枚' : '%' }}</span>
                            </div>
                            <div class="w-10 shrink h-10">
                                <button type="button" @click="deleteLostProduct(item.key)"
                                    class="h-full w-10 text-center">
                                    <XMarkIcon class="h-5 w-5 inline-block" />
                                </button>
                            </div>
                        </div>
                    </template>


                    <div class="text-center pt-2">
                        <button type="button" @click="addLostProduct()"
                            class="inline-block items-center max-w-60 w-full mx-auto py-2.5 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150 mr-2">
                            カード追加
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="file1" class="block font-medium text-sm text-neutral-700 mb-1">サムネイル <span
                            class="text-red-500">*</span></label>
                    <div class="text-center w-full">
                        <img v-if="url1" :src="url1" class="inline-block mt-4 h-52" />
                    </div>
                    <input @change="previewImage1" ref="photo1" id="file1" type="file" class="w-full" />
                    <div v-if="errors.thumbnail" class="text-red-500 text-sm mt-1">
                        {{ errors.thumbnail }}
                    </div>
                </div>

                <div class="mb-8">
                    <label for="file1" class="block font-medium text-sm text-neutral-700 mb-1">詳細ページ画像 <span
                            class="text-red-500">*</span></label>
                    <div class="text-center w-full">
                        <img v-if="url" :src="url" class="inline-block mt-4 h-52" />
                    </div>
                    <input @change="previewImage" ref="photo" id="file1" type="file" class="w-full" />
                    <div v-if="errors.image" class="text-red-500 text-sm mt-1">
                        {{ errors.image }}
                    </div>
                </div>

                <div class="mb-8">
                    <label for="file1" class="block font-medium text-sm text-neutral-700 mb-1">動画設定<span
                            class="text-red-500">*</span></label>
                    <div v-for="(video, index) in form.videos" class="flex items-center gap-3 py-1">

                        <input type="number"
                            class="h-9 w-32 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                            v-model="video.point" />以上&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;

                        <select v-model="video.level"
                            class="w-32 border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300">
                            <option v-for="(video_name, key) in video_names" :value="video_name">{{ video_name }}
                            </option>

                        </select>

                        <div class="w-10 shrink h-10">
                            <button type="button" @click="deleteVideo(index)" class="h-full w-10 text-center">
                                <XMarkIcon class="h-5 w-5 inline-block" />
                            </button>
                        </div>
                    </div>
                    <div class="text-center pt-2">
                        <button type="button" @click="addVideo()"
                            class="inline-block items-center px-8 py-2 bg-neutral-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-700 active:bg-neutral-700 focus:outline-none focus:border-neutral-700 focus:shadow-outline-neutral transition ease-in-out duration-150 mr-2">
                            追加
                        </button>
                    </div>
                </div>
                
                <div class="mb-8 text-center flex gap-2 justify-center sticky bottom-5">
                    <button type="submit"
                        class="inline-block items-center flex-1 max-w-60 py-2 bg-green-500 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        保存
                    </button>
                    <Link :href="route('admin.gacha', { cat_id: gacha.category_id })" as="button"
                        class="inline-block items-center flex-1 max-w-60 py-2 bg-rose-500 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-rose-700 active:bg-rose-700 focus:outline-none focus:shadow-outline-green transition ease-in-out duration-150">
                    戻る
                    </Link>
                </div>
            </form>

            <template v-if="form.lost_product_type == '0'">
                <h3 class="mb-2 font-bold">ラストワン賞 登録</h3>
                <hr />
                <div class="mb-6 py-2 border-neutral-200 border-b flex flex-wrap justify-center items-center gap-2">
                    <div v-if="product_last.id" class="flex-1 flex border-neutral-200 items-center">
                        <img :src="product_last.image" class="w-20 h-20 inline-block object-contain" />
                        <div class="flex flex-col justify-evenly text-sm py-1 px-2 ">
                            <div>{{ product_last.name }}</div>
                            <div>{{ product_last.rare }}</div>
                            <div class="text-red-500">{{ product_last.point }}pt</div>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-wrap gap-2 justify-center">
                        <button v-if="product_last.name" type="button" @click="modalProductLastOpen()"
                            class="items-center flex-1 min-w-16 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                            編集
                        </button>

                        <button v-else type="button" @click="modalProductLastOpen()"
                            class="items-center flex-1 max-w-60 py-2 my-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                            追加
                        </button>

                        <button v-if="product_last.name" @click="destroy_product_last(product_last.id)" type="button"
                            class="items-center flex-1 min-w-16 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                            削除
                        </button>
                    </div>
                </div>
            </template>


            <div class="flex flex-wrap justify-between mb-4 gap-2">
                <h3 class="font-bold pt-1">当たりカード 登録</h3>
                <div class="flex items-center gap-2">
                    <label for="sorting" class="block font-medium text-sm text-neutral-700 text-nowrap">ソート</label>
                    <select v-model="sorting" id="sorting"
                        class="text-sm w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300">
                        <option value="point_up">ポイント昇順</option>
                        <option value="point_down">ポイント降順</option>
                        <option value="count_up">{{ form.lost_product_type == '1' ? '排出率昇順' : 'カード枚数昇順' }}</option>
                        <option value="count_down">{{ form.lost_product_type == '1' ? '排出率降順' : 'カード枚数降順' }}</option>
                    </select>
                </div>
            </div>
            <hr class="mb-2" />
            <div v-for="(item, key) in sortedProducts"
                class="mb-2 pb-2 border-neutral-200 border-b flex items-center justify-between flex-wrap">
                <div class="flex border-neutral-200 items-center">
                    <img :src="item.image" class="w-20 h-20 inline-block object-contain" />
                    <div class="flex-1 inline-block text-sm py-1 px-2 ">
                        <div>ID: {{ item.id }} {{ item.rank ? `(${item.rank}等)` : '' }}</div>
                        <div>{{ item.name }}</div>
                        <div>{{ item.rare }}</div>
                        <div>{{ parseFloat((item.marks * count_step).toFixed(7)) }}
                            <span v-if="item.order">({{ item.order}}番)</span>&nbsp;
                            <span>(表示用: {{ parseFloat((item.category_id * count_step).toFixed(7)) }})</span>
                        </div>
                        <div class="text-red-500">
                            {{ format_number(item.point) }}pt
                            <span v-if="item.lost_type == '1'"> (発送専用)</span>
                        </div>
                    </div>
                </div>

                <div class="text-center flex flex-wrap gap-2">
                    <button type="button" @click="modalProductOpen(item)"
                        class="inline-block items-center flex-1 min-w-16 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150">
                        編集
                    </button>

                    <button type="button" @click="destroy_product_last(item.id)"
                        class="inline-block items-center flex-1 min-w-16 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                        削除
                    </button>
                </div>
            </div>
            <div class="flex justify-between text-center my-6 items-center gap-4">
                <span>還元率: {{ point_rate }}%</span>
                <button type="button" @click="modalProductOpen(0)"
                    class="inline-block items-center flex-1 max-w-48 py-2 bg-cyan-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 active:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:shadow-outline-cyan transition ease-in-out duration-150">
                    追加
                </button>
            </div>
        </div>

        <template>
            <TransitionRoot as="template" :show="open">
                <Dialog as="div" class="relative z-20" @close="open = false">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                        enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                        leave-to="opacity-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                    </TransitionChild>

                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                            <TransitionChild as="template" enter="ease-out duration-300"
                                enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                                leave-from="opacity-100 translate-y-0 sm:scale-100"
                                leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                <DialogPanel
                                    class="p-3 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                    <form @submit.prevent="submit_last()">
                                        <div class="mb-4">
                                            <label for="text1-1"
                                                class="block font-medium text-sm text-neutral-700 mb-1">名前<span
                                                    class="text-red-500">*</span></label>
                                            <input v-model="form_last.last_name" id="text1-1" type="text"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm  placeholder-neutral-300"
                                                placeholder="入力してください" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.last_name }}
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="text1-2"
                                                class="block font-medium text-sm text-neutral-700 mb-1">変換ポイント（半角数字）<span
                                                    class="text-red-500">*</span></label>
                                            <input v-model="form_last.last_point" id="text1-2" type="number"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="入力してください" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.last_point }}
                                            </div>
                                        </div>

                                        <div class="mb-4 flex items-center gap-2">
                                            <input id="lost_type" v-model="form_last.last_lost_type" type="checkbox" />
                                            <label for="lost_type" class="cursor-pointer text-sm py-1">
                                                発送専用
                                            </label>
                                        </div>

                                        <div v-if="form_last.is_last == 0" class="mb-4">
                                            <label for="last_rare"
                                                class="block font-medium text-sm text-neutral-700 mb-1">種類</label>
                                            <select v-model="form_last.last_rare" id="last_rare"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                                <option v-for="(lost_type, index) in lost_types" :value="lost_type">{{
                                                    lost_type }}</option>
                                            </select>
                                        </div>

                                        <!-- <div v-if="form_last.is_last == 0" class="mb-4">
                                            <label for="rank"
                                                class="block font-medium text-sm text-neutral-700 mb-1">カードランク</label>
                                            <select v-model="form_last.rank" id="rank"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                                <option value="0">その他</option>
                                                <option value="1">1等</option>
                                                <option value="2">2等</option>
                                                <option value="3">3等</option>
                                                <option value="4">4等</option>
                                            </select>
                                        </div> -->

                                        <div v-if="form_last.is_last == 0" class="mb-4">
                                            <label for="text1-4"
                                                class="block font-medium text-sm text-neutral-700 mb-1">{{
                                                    form.lost_product_type == '1' ? '排出率 (%)' : '枚数' }}（半角数字）<span
                                                    class="text-red-500">*</span></label>
                                            <input v-model="form_last.last_marks" id="text1-4" type="number"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="入力してください" :step="count_step" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.last_marks }}
                                            </div>
                                        </div>

                                        <!-- <div v-if="form_last.is_last == 0" class="mb-4">
                                            <label for="text1-4"
                                                class="block font-medium text-sm text-neutral-700 mb-1">{{
                                                    form.lost_product_type == '1' ? '表示用の排出率 (%)' : '表示用の在庫数' }}（半角数字）<span
                                                    class="text-red-500">*</span></label>
                                            <input v-model="form_last.category_id" id="text1-4" type="number"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="入力してください" :step="count_step" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.last_marks }}
                                            </div>
                                        </div> -->

                                        <!-- <div v-if="form_last.is_last == 0 && form.lost_product_type != '1'" class="mb-4">
                                            <label for="text1-4"
                                                class="block font-medium text-sm text-neutral-700 mb-1">排出順序</label>
                                            <input v-model="form_last.last_order" id="text1-4" type="number"
                                                class="w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm placeholder-neutral-300"
                                                placeholder="排出設定がない場合は、0を入力してください。" required />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.last_order }}
                                            </div>
                                        </div> -->

                                        <div class="mb-4">
                                            <label for="file1-1"
                                                class="block font-medium text-sm text-neutral-700 mb-1">画像アップロード <span
                                                    class="text-red-500">*</span></label>
                                            <div class="text-center w-full">
                                                <img v-if="url2" :src="url2" class="inline-block mt-4 h-24" />
                                            </div>
                                            <input @change="previewImage2" ref="photo2" id="file1-1" type="file"
                                                class="w-full" />
                                            <div class="text-red-500 text-sm mt-1">
                                                {{ errors.last_image }}
                                            </div>
                                        </div>
                                        <div class="mb-6 text-center">
                                            <button type="submit"
                                                class="inline-block items-center px-8 py-2.5 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 mr-2">
                                                保存
                                            </button>

                                            <button type="button" @click="open = false"
                                                class="inline-block items-center px-8 py-2.5 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150">
                                                キャンセル
                                            </button>
                                        </div>
                                    </form>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
        </template>

    </AppLayout>
</template>

<script>
import { Head, useForm, usePage, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/Admin.vue';
import { Inertia } from '@inertiajs/inertia';

import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import { useConfirm } from "@/composables/useConfirm";

const { confirm, alert } = useConfirm();
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';


export default {
    components: { Link, Head, AppLayout, Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot, ExclamationTriangleIcon, XMarkIcon, VueDatePicker },
    props: {
        errors: Object,
        gacha: Object,
        category_share: Object,
        product_last: Object,
        products: Object,
        productsLostSetting: Object,
        videos: Object,
        titles: Object,
        ranks: Object,
        lost_types: Object,
        video_names: Object,
        plans: Object,
    },
    data() {
        return {
            url: null,
            url1: null,
            url2: null,
            open: false,
            sorting: 'point_down'
        }
    },
    methods: {
        format_number(n) {
            return String(n).replace(/(.)(?=(\d{3})+$)/g, '$1,');
        },
        addVideo() {
            if (this.video_names.length == 0) {
                alert('登録された動画がありません。');
                return;
            }
            this.form.videos.push({
                level: this.video_names[0],
                point: 0
            })
        },
        deleteVideo(id) {
            this.form.videos = this.form.videos.filter((item, index) => { return index != id })
        },
        destroy_product_last(id) {
            confirm("削除してもいいですか？", '', 'error').then((result) => {
                if (result) {
                    Inertia.delete(route('admin.gacha.product_last.destroy', { id: id }));
                }
            });
        },
        modalProductLastOpen() {
            if (this.product_last.name) {
                this.form_last.last_name = this.product_last.name;
                this.form_last.last_point = this.product_last.point;
                this.form_last.last_rare = this.product_last.rare;
                this.form_last.last_image = "";
                this.form_last.is_update = 1;
                this.url2 = this.product_last.image;
            } else {
                this.form_last.last_name = "";
                this.form_last.last_point = "";
                this.form_last.last_rare = "";
                this.form_last.last_image = "";
                this.form_last.is_update = 0;
                this.url2 = "";
            }
            this.form_last.is_last = 1;
            this.form_last.last_marks = 0;
            this.open = true;
        },
        modalProductOpen(product) {
            if (product) {
                this.form_last.last_id = product.id;
                this.form_last.last_name = product.name;
                this.form_last.last_point = product.point;
                this.form_last.last_rare = product.rare;
                this.form_last.last_marks = parseFloat((product.marks * this.count_step).toFixed(7));
                this.form_last.category_id = parseFloat((product.category_id * this.count_step).toFixed(7));
                this.form_last.last_order = product.order;
                this.form_last.last_image = "";
                this.form_last.is_update = 1;
                this.url2 = product.image;
                this.form_last.rank = product.rank;
                this.form_last.last_lost_type = product.lost_type == 1;
            } else {
                this.form_last.last_id = "";
                this.form_last.last_name = "";
                this.form_last.last_point = "";
                this.form_last.last_rare = "";
                this.form_last.last_marks = 0;
                this.form_last.last_order = 0;
                this.form_last.last_image = "";
                this.form_last.is_update = 0;
                this.url2 = "";
                this.form_last.rank = 0;
                this.form_last.category_id = 0;
                this.form_last.last_lost_type = false;
            }
            this.form_last.is_last = 0;
            this.open = true;
        },
        closeModal() {
            this.open = false;
        },
        submit_last() {
            if (this.form_last.is_last == 1) {
                if (this.$refs.photo2.files[0]) {
                    this.form_last.last_image = this.$refs.photo2.files[0];
                }
                this.form_last.post(route('admin.gacha.product_last.create'), {
                    preserveScroll: true,
                    onFinish: () => {
                    },
                });
            } else {
                if (this.$refs.photo2.files[0]) {
                    this.form_last.last_image = this.$refs.photo2.files[0];
                }
                if (this.form_last.last_order > 0 && this.form_last.last_marks != 1) {
                    alert('排出順序は点数の値が1の場合にのみ設定できます。');
                    return;
                }
                if (this.form_last.last_order > 0 && this.form_last.last_order <= this.gacha.count) {
                    alert('このガチャはすでに ' + this.gacha.count + ' 回回されています。 ' + this.gacha.count + ' 以上を設定する必要があります。');
                    return;
                }
                if (this.form_last.last_order > 0 && this.products.data.find((item) => { return item.order == this.form_last.last_order && item.id != this.form_last.last_id; })) {
                    alert('この排出順序はすでに使用しました。');
                    return;
                }
                let form1 = useForm(this.form_last.data());
                form1.last_marks /= this.count_step;
                form1.category_id /= this.count_step;
                form1.post(route('admin.gacha.product.create'), {
                    onFinish: () => {
                    },
                    preserveScroll: true
                });
            }

        },

        submit() {
            let count_rest = this.form.count_card - this.gacha.count;
            var count_lost = 0; let i;
            for (i = 0; i < this.form.lostProducts.length; i++) {
                if (this.form.lostProducts[i].key) {
                    count_lost = count_lost + this.form.lostProducts[i].count;
                }
            }
            let count_product = 0;
            for (i = 0; i < this.products.data.length; i++) {
                if (this.form.lost_product_type != '1' || this.products.data[i].order == 0) {
                    count_product = count_product + Math.max(0, this.products.data[i].marks) * this.count_step;
                }
            }

            if (this.form.lost_product_type == '1') this.form.count_card = 0;

            let count_all = count_lost + count_product;
            count_all = parseFloat(count_all.toFixed(7));

            let content = '';
            if (this.form.lost_product_type == '1') {
                content = count_all == 100 ?
                'カード排出率の合計が正確です。' :
                `カード排出率の合計は100％でなければなりません。現在${count_all}%です。`;
                this.form.spin_limit = 0;
            }
            else content = count_all == count_rest ?
                '残り口数 ' + count_rest + ' = 当たりカードの残り数 ' + count_product + " + カード指定の残り数 " + count_lost + '\n保存しますか？' :
                '残り口数 ' + count_rest + ' ≠ 当たりカードの残り数 ' + count_product + " + カード指定の残り数 " + count_lost + "\n大丈夫ですか？";

            confirm('', content, count_all == count_rest ? 'success' : 'warning').then((result) => {
                if (result) {
                    if (this.$refs.photo) {
                        this.form.image = this.$refs.photo.files[0];
                    }
                    if (this.$refs.photo1) {
                        this.form.thumbnail = this.$refs.photo1.files[0];
                    }
                    let form1 = useForm({
                        ...this.form.data(),
                        lostProducts: this.form.lostProducts.map(item => {
                            return {
                                ...item,
                                count: item.count / this.count_step
                            }
                        })
                    });

                    form1.post(route('admin.gacha.update'), { preserveScroll: true });
                }
            });
        },

        previewImage(e) {
            const file = e.target.files[0];
            this.url = URL.createObjectURL(file);
        },

        previewImage1(e) {
            const file = e.target.files[0];
            this.url1 = URL.createObjectURL(file);
        },

        previewImage2(e) {
            const file = e.target.files[0];
            this.url2 = URL.createObjectURL(file);
        },

        toPublic(to_status) {
            this.form_to_public.to_status = to_status;
            this.form_to_public.post(route('admin.gacha.to_public'), {
                onFinish: () => {

                },
                preserveScroll: true
            });
        },

        gachaLimit(to_status) {
            this.form_to_limit.to_status = to_status;
            this.form_to_limit.post(route('admin.gacha.gacha_limit'), {
                onFinish: () => {

                },
                preserveScroll: true
            });
        },

        addLostProduct() {
            let key = 0;
            let i;
            for (i = 0; i < this.form.lostProducts.length; i++) {
                if (key < this.form.lostProducts[i].key) {
                    key = this.form.lostProducts[i].key;
                }
            }
            key = key + 1;
            this.form.lostProducts.push({ id: 0, point: 0, count: 0, key: key });
        },
        deleteLostProduct(key) {
            let i;
            for (i = 0; i < this.form.lostProducts.length; i++) {
                if (key == this.form.lostProducts[i].key) {
                    this.form.lostProducts[i].key = 0;
                    break;
                }
            }
        },
        copyLink(text) {
            navigator.clipboard.writeText(text).then(() => {
                toast('URLをコピーしました。', {
                    autoClose: 2000
                });
            });
        }
    },
    setup(props) {
        const form = useForm({
            id: props.gacha.id,
            title: props.gacha.title,
            point: props.gacha.point,
            consume_point: props.gacha.consume_point,
            count_card: props.gacha.count_card,
            count: props.gacha.count,
            lost_product_type: props.gacha.lost_product_type,
            thumbnail: '',
            image: '',
            category_id: props.category_share.cat_id,
            lostProducts: [],
            spin_limit: props.gacha.spin_limit,
            ids: [],
            videos: props.videos,
            start_time: props.gacha.start_time,
            end_time: props.gacha.end_time,
            rank_limit: props.gacha.rank_limit,
            plan_limit: props.gacha.plan_limit,
        });

        let form_last_data = {
            last_id: "",
            last_name: "",
            last_point: "",
            last_rare: "",
            last_marks: "",
            last_image: "",
            last_order: "",
            is_last: 0,
            gacha_id: props.gacha.id,
            is_update: 0,
            rank: 0,
            category_id: 0,
            last_lost_type: false,
        };

        const form_last = useForm(form_last_data);

        const form_to_public = useForm({ gacha_id: props.gacha.id, to_status: 1 });
        const form_to_limit = useForm({ gacha_id: props.gacha.id, to_status: 1 });

        return { form, form_last, form_to_public, form_to_limit }
    },
    mounted() {
        this.url = this.gacha.image;
        this.url1 = this.gacha.thumbnail;

        this.form.lostProducts = [];
        let i;
        for (i = 0; i < this.productsLostSetting.length; i++) {
            let item = this.productsLostSetting[i];
            this.form.lostProducts.push({ id: item.id, count: parseFloat((item.count * this.count_step).toFixed(7)), point: item.point, key: item.id });
        }
    },
    computed: {
        count_step() {
            if (this.form.lost_product_type == '1') return 0.01;
            return 1;
        },
        sortedProducts() {
            let products = this.products.data;
            if (this.sorting == 'point_up') products.sort((a, b) => a.point - b.point);
            if (this.sorting == 'point_down') products.sort((a, b) => b.point - a.point);
            if (this.sorting == 'count_up') products.sort((a, b) => a.marks - b.marks);
            if (this.sorting == 'count_down') products.sort((a, b) => b.marks - a.marks);
            return products;
        },
        point_rate() {
            let total_point = this.form.point * (this.form.count_card - this.gacha.count);
            if (this.form.lost_product_type == '1') total_point = this.form.point * 10000;
            let card_point = 0;
            this.form.lostProducts.forEach(item => {
                card_point += item.point * item.count / this.count_step;
            })
            this.products.data.forEach(item => {
                if (item.marks > 0) card_point += item.point * item.marks / this.count_step;
            })
            return (card_point * 100.0 / total_point).toFixed(2);
        }
    }
}
</script>
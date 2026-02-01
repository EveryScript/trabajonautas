<div x-show="step === 2" x-cloak x-transition:enter.duration.300ms>
                <h5 class="mb-2 font-bold text-md dark:text-white">¿Cuál es tu grado académico?</h5>
                <ul class="grid grid-cols-1 gap-1 mx-auto mb-8 md:grid-cols-2 lg:grid-cols-3">
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="1" id="profile-1"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-1"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Estudiante</span>
                                <p class="text-xs dark:text-tbn-light">Bachiller o en instituto o universidad</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="2" id="profile-2"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-2"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Técnico Medio</span>
                                <p class="text-xs dark:text-tbn-light">Profesional titulado a nivel técnico medio</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="3" id="profile-3"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-3"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Técnico Superior</span>
                                <p class="text-xs dark:text-tbn-light">Profesional titulado a nivel técnico superior
                                </p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="4" id="profile-4"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-4"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Egresado</span>
                                <p class="m-0 text-xs dark:text-tbn-light">Aprobó todas las materias y solamente le
                                    falta la tesis.</p>
                            </div>
                        </label>
                    </li>
                    <li class="text-center">
                        <input type="radio"x-model='grade_profile_id' value="5" id="profile-5"
                            name="grade-profile" class="hidden peer">
                        <label for="profile-5"
                            class="flex justify-center items-center h-24 sm:h-[8rem] px-5 py-3 text-tbn-secondary dark:text-white bg-white dark:bg-tbn-dark border border-tbn-light dark:border-tbn-secondary rounded-lg cursor-pointer  peer-checked:border-tbn-primary peer-checked:text-tbn-primary hover:bg-tbn-light dark:hover:text-tbn-light dark:hover:bg-neutral-900">
                            <div>
                                <span class="block font-bold uppercase">Titulado</span>
                                <p class="text-xs dark:text-tbn-light">Actualmente con titulo en provisión nacional.
                                </p>
                            </div>
                        </label>
                    </li>
                </ul>
                <div class="flex justify-between mt-4">
                    <x-secondary-button type="button" x-on:click="step = 1"> Anterior</x-secondary-button>
                    <x-button type="button" x-on:click="step = 3" x-bind:disabled="!grade_profile_id">
                        Siguiente</x-button>
                </div>
            </div>
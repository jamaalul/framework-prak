<div class="block">
    <flux:sidebar sticky collapsible
        class="h-full bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand href="#"
                logo="https://upload.wikimedia.org/wikipedia/en/9/96/Meme_Man_on_transparent_background.webp"
                name="FrameWOK" />

            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" :current="!$model">Home
            </flux:sidebar.item>
            @if ($menuVisibility['JenisHewan'] ?? false)
                <flux:sidebar.item icon="inbox" href="{{ route('dashboard', ['model' => 'JenisHewan']) }}"
                    :current="$model === 'JenisHewan'">Jenis Hewan</flux:sidebar.item>
            @endif
            @if ($menuVisibility['RasHewan'] ?? false)
                <flux:sidebar.item icon="globe-asia-australia" href="{{ route('dashboard', ['model' => 'RasHewan']) }}"
                    :current="$model === 'RasHewan'">Ras Hewan</flux:sidebar.item>
            @endif
            @if ($menuVisibility['Kategori'] ?? false)
                <flux:sidebar.item icon="folder" href="{{ route('dashboard', ['model' => 'Kategori']) }}"
                    :current="$model === 'Kategori'">Kategori</flux:sidebar.item>
            @endif
            @if ($menuVisibility['KategoriKlinis'] ?? false)
                <flux:sidebar.item icon="folder-plus" href="{{ route('dashboard', ['model' => 'KategoriKlinis']) }}"
                    :current="$model === 'KategoriKlinis'">Kategori Klinis</flux:sidebar.item>
            @endif
            @if ($menuVisibility['KodeTindakanTerapi'] ?? false)
                <flux:sidebar.item icon="book-open" href="{{ route('dashboard', ['model' => 'KodeTindakanTerapi']) }}"
                    :current="$model === 'KodeTindakanTerapi'">Kode Tindakan Terapi</flux:sidebar.item>
            @endif
            @if ($menuVisibility['Pet'] ?? false)
                <flux:sidebar.item icon="swatch" href="{{ route('dashboard', ['model' => 'Pet']) }}"
                    :current="$model === 'Pet'">Pet</flux:sidebar.item>
            @endif
            @if ($menuVisibility['Role'] ?? false)
                <flux:sidebar.item icon="identification" href="{{ route('dashboard', ['model' => 'Role']) }}"
                    :current="$model === 'Role'">Role</flux:sidebar.item>
            @endif
            @if ($menuVisibility['User'] ?? false)
                <flux:sidebar.item icon="user" href="{{ route('dashboard', ['model' => 'User']) }}"
                    :current="$model === 'User'">User</flux:sidebar.item>
            @endif
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
            <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile
                avatar="https://images.unsplash.com/photo-1728577740843-5f29c7586afe?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=880"
                name="{{ $user->nama }}" />

            <flux:menu>
                <flux:menu.radio.group>
                    <flux:menu.radio>{{ $user->nama }}</flux:menu.radio>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>
</div>

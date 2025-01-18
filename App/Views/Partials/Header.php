<?php
$isLogged = $_SESSION['user'] ?? null;
?>

<header class="sticky top-0 z-10 bg-gray-800 border-b border-gray-700">
    <div class="container flex items-center justify-between px-4 py-4 mx-auto">
        <!-- Logo -->
        <a href="/home" class="text-2xl font-bold text-blue-400">YOUDEMY</a>

        <!-- Desktop Navigation -->
        <nav class="items-center justify-center hidden space-x-6 md:flex">
            <a href="/catalog" class="transition-colors hover:text-blue-400">Catalog</a>
            <?php if ($isLogged): ?>
                <a href="/mycourses" class="transition-colors hover:text-blue-400">My Courses</a>
                <a href="?action=auth_logout" class="px-4 py-2 transition-colors bg-blue-600 rounded hover:bg-blue-700">Log out</a>
            <?php else: ?>
                <a href="/login" class="transition-colors hover:text-blue-400">Log in</a>
                <a href="/signup" class="px-4 py-2 transition-colors bg-blue-600 rounded hover:bg-blue-700">Sign up</a>
            <?php endif; ?>
        </nav>

        <!-- Mobile Menu Button -->
        <button id="openNav" class="text-gray-300 md:hidden hover:text-white">
            <span class="icon-[mdi--hamburger-menu] text-4xl"></span>
        </button>
    </div>

    <!-- Mobile Navigation -->
    <nav
        id="mobileNav"
        class="fixed top-0 flex-col items-center justify-center hidden w-full px-4 py-6 space-y-4 bg-gray-800 border-t border-gray-700 h-fit">
        <a href="/catalog" class="block text-gray-300 transition-colors hover:text-blue-400">Catalog</a>
        <?php if ($isLogged): ?>
            <a href="/mycourses" class="transition-colors hover:text-blue-400">My Courses</a>
        <?php else: ?>
            <a href="?action=auth_logout" class="w-full px-4 py-2 text-center transition-colors bg-blue-600 rounded hover:bg-blue-700">Log out</a>
            <a href="/login" class="block text-gray-300 transition-colors hover:text-blue-400">Log in</a>
            <a href="/signup" class="w-full px-4 py-2 text-center transition-colors bg-blue-600 rounded hover:bg-blue-700">Sign up</a>
        <?php endif; ?>
    </nav>
</header>

<?php require_once __DIR__ . '/../Helper/Message.php' ?>
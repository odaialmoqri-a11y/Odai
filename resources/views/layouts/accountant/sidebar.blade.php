{{-- SPDX-License-Identifier: MIT --}}
<div class="w-full lg:w-48 md:w-48 bg-blue-700 text-white h-full accountant-sidebar">
  <div class="min-h-full header-wrapper-b hidden lg:block md:block ">
   @include('layouts.accountant.menu')
  </div>
</div>
<div id="res_sidebar" class="w-full lg:w-48 md:w-48  hidden lg:hidden md:hidden res_sidebar">
  <div class="min-h-full header-wrapper-b lg:hidden md:hidden accountant-sidebar text-white">
   @include('layouts.accountant.menu')
  </div>
</div>
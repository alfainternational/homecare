@extends('layouts.app')
@section('title', 'من نحن — WarmConcierge')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-20 text-center">
  <h1 class="text-4xl font-black text-gray-900 mb-6">من نحن</h1>
  <p class="text-xl text-gray-500 leading-relaxed mb-8">وورم كونسيرج هي منصة خدمات الصيانة المنزلية الرائدة في المملكة. نربط أصحاب المنازل بفنيين معتمدين لتوفير أفضل تجربة صيانة منزلية.</p>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center mt-16">
    @foreach([['🏆','رسالتنا','توفير صيانة منزلية موثوقة باحترافية عالية'],['👷','فريقنا','أكثر من 50 فني معتمد في مختلف التخصصات'],['🌟','رؤيتنا','أن نكون الخيار الأول لكل بيت في المملكة']] as $v)
      <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        <div class="text-4xl mb-4">{{ $v[0] }}</div>
        <h3 class="font-bold text-gray-800 text-xl mb-2">{{ $v[1] }}</h3>
        <p class="text-gray-500 text-sm">{{ $v[2] }}</p>
      </div>
    @endforeach
  </div>
</div>
@endsection

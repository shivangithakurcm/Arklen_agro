@extends('layouts.app')
@section('title', 'Binary Tree — Arklen Agro')
@section('page-title', 'Binary Tree')

@section('content')
<div class="card card-pad">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="margin:0;color:var(--green-800);"><i class="fas fa-sitemap"></i> Member Binary Tree</h3>
        <div style="display:flex;gap:10px;align-items:center;">
            <span style="font-size:12px;color:#888;">Zoom:</span>
            <button onclick="zoom(-0.1)" class="btn btn-secondary btn-sm"><i class="fas fa-minus"></i></button>
            <button onclick="zoom(0.1)"  class="btn btn-secondary btn-sm"><i class="fas fa-plus"></i></button>
            <button onclick="resetZoom()" class="btn btn-secondary btn-sm"><i class="fas fa-expand"></i></button>
        </div>
    </div>
    <div id="tree-scroll" style="overflow:auto;min-height:500px;cursor:grab;background:var(--green-50);border-radius:10px;border:1px solid var(--green-200);padding:30px;">
        <div id="tree-container" style="display:inline-block;transform-origin:top left;transition:transform .2s;"></div>
    </div>
</div>
@endsection

@push('scripts')
<style>
#tree-scroll:active { cursor: grabbing; }
.tnode-wrap { display:flex; flex-direction:column; align-items:center; position:relative; }
.tnode { background:#fff; border:2px solid var(--green-400); border-radius:12px; padding:10px 18px; text-align:center; min-width:120px; box-shadow:0 2px 8px rgba(0,0,0,.08); position:relative; z-index:2; transition:transform .15s,box-shadow .15s; cursor:pointer; }
.tnode:hover { transform:translateY(-2px); box-shadow:0 4px 16px rgba(65,111,25,.2); border-color:var(--green-600); }
.tnode-id { font-weight:800; font-size:13px; color:var(--green-800); letter-spacing:.3px; }
.tnode-name { font-size:11px; color:var(--green-600); margin-top:3px; font-weight:500; }
.tnode-wrap > .tnode::after { content:''; position:absolute; bottom:-22px; left:50%; transform:translateX(-50%); width:2px; height:22px; background:var(--green-300); }
.tchildren { display:flex; gap:0; position:relative; margin-top:22px; }
.tchildren::before { content:''; position:absolute; top:0; left:0; width:100%; height:2px; background:var(--green-300); }
.tbranch { display:flex; flex-direction:column; align-items:center; position:relative; padding:0 20px; }
.tbranch::before { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); width:2px; height:22px; background:var(--green-300); }
.tleg { font-size:10px; font-weight:800; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:6px; margin-top:22px; position:relative; z-index:2; }
.tleg.L { background:var(--green-100); color:var(--green-800); border:1.5px solid var(--green-400); }
.tleg.R { background:#fff3e0; color:#b45309; border:1.5px solid #fcd59a; }
.tempty { background:#fafafa; border:1.5px dashed #d1d5db; border-radius:10px; padding:8px 14px; min-width:100px; text-align:center; font-size:11px; color:#bbb; font-style:italic; }
</style>

<script>
const members = @json($members);
let scale = 1;

function buildTree(sellerId, depth) {
    depth = depth || 0;
    if (depth > 12) return '';
    const node = members[sellerId];
    if (!node) return '';

    const name = node.first_name + ' ' + node.last_name;
    const allMembers = Object.values(members);
    const left  = allMembers.find(m => m.sponsor_id === sellerId && m.position === 'left');
    const right = allMembers.find(m => m.sponsor_id === sellerId && m.position === 'right');

    const leftHtml  = left  ? buildTree(left.seller_id,  depth+1) : '<div class="tempty">Empty</div>';
    const rightHtml = right ? buildTree(right.seller_id, depth+1) : '<div class="tempty">Empty</div>';

    return '<div class="tnode-wrap">' +
        '<div class="tnode">' +
            '<div class="tnode-id">' + node.seller_id + '</div>' +
            '<div class="tnode-name">' + name + '</div>' +
        '</div>' +
        '<div class="tchildren">' +
            '<div class="tbranch"><div class="tleg L">L</div>' + leftHtml + '</div>' +
            '<div class="tbranch"><div class="tleg R">R</div>' + rightHtml + '</div>' +
        '</div>' +
    '</div>';
}

function findRoots() {
    const allIds = new Set(Object.keys(members));
    return Object.values(members).filter(function(m) {
        return !m.sponsor_id || !allIds.has(m.sponsor_id);
    });
}

window.addEventListener('DOMContentLoaded', function() {
    const roots = findRoots();
    let html = '';

    if (roots.length === 1) {
        html = buildTree(roots[0].seller_id, 0);
    } else {
        const branches = roots.map(function(r) {
            return '<div class="tbranch"><div class="tleg L" style="background:var(--green-700);color:#fff;border-color:var(--green-800);">' +
                r.seller_id.substring(0,3) +
            '</div>' + buildTree(r.seller_id, 0) + '</div>';
        }).join('');

        html = '<div class="tnode-wrap">' +
           '<div class="tnode" style="background:var(--green-800);border-color:var(--green-900);padding:12px 20px;">' +
    '<img src="/images/arklen-logo.png" style="width:40px;height:40px;object-fit:contain;display:block;margin:0 auto 6px;">' +
    '<div class="tnode-id" style="color:#fff;">ARKLEN</div>' +
    '<div class="tnode-name" style="color:#9ac46b;">Agro Pvt. Ltd</div>' +
'</div>' +
            '<div class="tchildren">' + branches + '</div>' +
        '</div>';
    }

    document.getElementById('tree-container').innerHTML =
        '<div style="padding:20px;display:flex;justify-content:center;">' + html + '</div>';
});

function zoom(delta) {
    scale = Math.min(2, Math.max(0.3, scale + delta));
    document.getElementById('tree-container').style.transform = 'scale(' + scale + ')';
}
function resetZoom() {
    scale = 1;
    document.getElementById('tree-container').style.transform = 'scale(1)';
}

const scroll = document.getElementById('tree-scroll');
let isDown = false, startX, startY, scrollLeft, scrollTop;
scroll.addEventListener('mousedown', function(e) { isDown=true; startX=e.pageX-scroll.offsetLeft; startY=e.pageY-scroll.offsetTop; scrollLeft=scroll.scrollLeft; scrollTop=scroll.scrollTop; });
scroll.addEventListener('mouseleave', function() { isDown=false; });
scroll.addEventListener('mouseup',    function() { isDown=false; });
scroll.addEventListener('mousemove',  function(e) { if(!isDown) return; e.preventDefault(); scroll.scrollLeft=scrollLeft-(e.pageX-scroll.offsetLeft-startX); scroll.scrollTop=scrollTop-(e.pageY-scroll.offsetTop-startY); });
</script>
@endpush
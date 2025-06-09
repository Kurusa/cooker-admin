let chart = null;
let rootNode = null;

function buildTreeNode(category) {
    const node = {
        id: category.id,
        innerHTML: `<span class="badge" data-id="${category.id}">${category.title} (${category.children_count})</span>`,
        HTMLclass: category.has_children ? 'collapsed' : 'leaf',
    };

    if (category.has_children) {
        node.collapsed = true;
        node.children = [{ pseudo: true }];
    }

    return node;
}

function renderTree() {
    if (chart) chart.destroy();

    chart = new Treant({
        chart: {
            container: "#tree-container",
            rootOrientation: "WEST",
            animateOnInit: true,
            node: { collapsable: true },
            animation: {
                nodeAnimation: "easeOutBack",
                nodeSpeed: 500,
                connectorsAnimation: "bounce",
                connectorsSpeed: 500
            },
            connectors: { type: "step" }
        },
        nodeStructure: rootNode
    });
}

function findNodeById(node, id) {
    if (node.id === id) return node;
    if (!node.children) return null;

    for (const child of node.children) {
        const found = findNodeById(child, id);
        if (found) return found;
    }

    return null;
}

function loadChildren(categoryId) {
    const targetNode = findNodeById(rootNode, categoryId);
    if (!targetNode || !targetNode.children?.[0]?.pseudo) return;

    fetch('/categories/children/' + categoryId)
        .then(r => r.json())
        .then(children => {
            targetNode.children = children.map(buildTreeNode);
            targetNode.collapsed = false;
            targetNode.HTMLclass = 'expanded';
            renderTree();
        });
}

document.addEventListener('DOMContentLoaded', () => {
    fetch('/categories/children')
        .then(r => r.json())
        .then(categories => {
            rootNode = {
                text: { name: "Категорії" },
                HTMLclass: 'expanded',
                collapsed: false,
                children: categories.map(buildTreeNode)
            };
            renderTree();
        });

    document.body.addEventListener('click', (e) => {
        const target = e.target;
        if (!target.classList.contains('collapse-switch')) return;

        const parentNode = target.closest('.node');
        const badge = parentNode?.querySelector('.badge');
        if (!badge) return;

        const categoryId = parseInt(badge.dataset.id);
        loadChildren(categoryId);
    });

});
document.body.addEventListener('click', (e) => {
    const badge = e.target.closest('.badge');
    if (!badge) return;

    const categoryId = parseInt(badge.dataset.id);
    if (!categoryId) return;

    loadChildren(categoryId);
});

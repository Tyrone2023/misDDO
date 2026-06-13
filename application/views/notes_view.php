            <?php include('templates/head.php'); ?>
            <?php include('templates/header.php'); ?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <!-- <a href="<?= $link; ?>" class="btn btn-success waves-effect width-md waves-light"><?= $pn; ?></a> -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="mb-4">My Notes</h3>

                                        <?php if ($this->session->flashdata('success')): ?>
                                            <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
                                        <?php endif; ?>

                                        <?php if ($action == 'index'): ?>
                                            <a href="<?= site_url('note/create') ?>" class="btn btn-primary mb-3">Create New Note</a>

                                            <?php if (!empty($notes)): ?>
                                                <?php foreach ($notes as $note): ?>
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <h4><?= htmlspecialchars($note->title) ?></h4>
                                                            <p><?= strip_tags(word_limiter($note->content, 50)) ?></p>
                                                            <small class="text-muted">Created on: <?= date('F j, Y, g:i a', strtotime($note->created_at)) ?></small>
                                                            <div class="mt-2">
                                                                <a href="<?= site_url('note/edit/' . $note->id) ?>" class="btn btn-sm btn-warning">Edit</a>
                                                                <a href="<?= site_url('note/delete/' . $note->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this note?')">Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p>No notes found. <a href="<?= site_url('note/create') ?>">Create one</a>.</p>
                                            <?php endif; ?>

                                        <?php elseif ($action == 'create' || $action == 'edit'): ?>
                                            <h4><?= ($action == 'create') ? 'Create New Note' : 'Edit Note' ?></h4>

                                            <form action="<?= ($action == 'create') ? site_url('note/store') : site_url('note/update/' . $note->id) ?>" method="POST">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="title" name="title" required value="<?= ($action == 'edit') ? htmlspecialchars($note->title) : '' ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="content" class="form-label">Content</label>
                                                    <textarea id="content" name="content" class="form-control" rows="6"><?= ($action == 'edit') ? htmlspecialchars($note->content) : '' ?></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-success"><?= ($action == 'create') ? 'Save Note' : 'Update Note' ?></button>
                                                <a href="<?= site_url('note') ?>" class="btn btn-secondary">Back</a>
                                            </form>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->

                        <!-- end container-fluid -->

                    </div>
                    <!-- end content -->

                    <?php include('templates/footer.php'); ?>
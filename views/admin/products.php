<?php 
// 画像で指定された「機能の使用方法」に基づきヘッダーを読み込み
// ディレクトリ構成(views/admin/ から views/layout/ への参照)に合わせてパスを調整しています
include __DIR__ . '/../layout/header.php'; 
?>

<main class="main-content" style="background-color: #F4F6F8; padding: 20px; color: #333333;"> <h2 style="margin-bottom: 20px;">商品設定</h2> <section style="background: #FFFFFF; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;"> <h3 style="border-left: 4px solid #00A4E5; padding-left: 10px; margin-bottom: 15px;">新規商品登録</h3>
        
        <form action="store.php" method="POST" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;"> <div>
                <label style="display: block; font-size: 0.9em;">商品名</label> <input type="text" name="name" placeholder="例: コーヒー" required 
                       style="padding: 8px; border: 1px solid #ddd; border-radius: 8px; width: 200px;"> </div>
            
            <div>
                <label style="display: block; font-size: 0.9em;">価格</label> <input type="number" name="price" placeholder="¥" required 
                       style="padding: 8px; border: 1px solid #ddd; border-radius: 8px; width: 100px;"> </div>
            
            <div>
                <label style="display: block; font-size: 0.9em;">カテゴリ</label> <select name="category_id" style="padding: 8px; border: 1px solid #ddd; border-radius: 8px;">
                    <option value="">選択してください</option>
                    <option value="1">フード</option>
                    <option value="2">ドリンク</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; font-size: 0.9em; margin-bottom: 5px;">区分設定</label> <div style="font-size: 0.9em;">
                    <label><input type="radio" name="is_takeout" value="0" checked> 店内のみ(10%)</label> <label style="margin-left: 10px;"><input type="radio" name="is_takeout" value="1"> テイクアウトのみ(8%)</label> <label style="margin-left: 10px;"><input type="radio" name="is_takeout" value="2"> 共通</label> </div>
            </div>
            
            <button type="submit" class="btn-primary" 
                    style="background-color: #00A4E5; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;"> + この内容で登録する
            </button>
        </form>
    </section>

    <section style="background: #FFFFFF; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"> <h3 style="margin-bottom: 15px;">登録済み商品一覧</h3>
        
        <div style="margin-bottom: 15px; display: flex; gap: 10px; align-items: center;">
            絞込: <select style="padding: 5px; border-radius: 8px;"><option>カテゴリすべて</option></select> 検索: <input type="text" placeholder="商品名を入力..." style="padding: 5px; border: 1px solid #ddd; border-radius: 8px;"> </div>

        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #E9ECEF; border-bottom: 2px solid #ddd;"> <th style="padding: 12px;">ID</th> <th style="padding: 12px;">商品名</th>
                    <th style="padding: 12px;">価格</th>
                    <th style="padding: 12px;">カテゴリ</th>
                    <th style="padding: 12px;">区分</th>
                    <th style="padding: 12px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #eee;"> <td style="padding: 12px;">1</td>
                    <td style="padding: 12px;">コーヒー</td>
                    <td style="padding: 12px;">¥400</td>
                    <td style="padding: 12px;">ドリンク</td>
                    <td style="padding: 12px;">共通</td>
                    <td style="padding: 12px;">
                        <form action="delete.php" method="POST" onsubmit="return confirm('本当に削除しますか？')">
                            <input type="hidden" name="id" value="1">
                            <button type="submit" style="color: #DC3545; background: none; border: 1px solid #DC3545; padding: 5px 10px; border-radius: 8px; cursor: pointer;">
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</main>

<?php 
// 画像で指定された「機能の使用方法」に基づきフッターを読み込み
include __DIR__ . '/../layout/footer.php'; 
?>